<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;
use Nette\Utils\Random;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use Smalot\PdfParser\Parser;
use App\Models\User;
use DB;
use Exception;
use OpenAI;
use Illuminate\Support\Facades\Http;

class AiController extends Controller
{

   public function uploadCV(Request $request)
{
    try {
        // 1. Validate input
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        // 2. Store file
        $path = $request->file('file')->store('cvs', 'public');

        // 3. Extract text from PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($request->file('file')->getPathname());
        $text = $pdf->getText() ?? '';

        if (empty(trim($text))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to extract text from the PDF. Please upload a text-based CV.',
            ], 422);
        }

        // 4. Build prompt with CV validation
        $prompt = <<<PROMPT
You are an expert HR professional.

Step 1: Determine if the provided text is a CV/resume.
- A CV/resume usually includes personal details, work experience, education, skills, etc.
- If it is NOT a CV/resume, return ONLY this JSON (do not add extra text):

{
  "is_cv": false,
  "message": "Looks like you uploaded a wrong file. Please upload a valid CV in PDF format."
}

Step 2: If it IS a CV/resume, review it and return ONLY this JSON object (do not add extra text):

{
  "is_cv": true,
  "strengths": ["bullet 1", "bullet 2"],
  "weaknesses": ["bullet 1", "bullet 2"],
  "suggestions": ["bullet 1", "bullet 2"],
  "impression": "summarize overall impression.",
  "score": 0
}

Requirements:
- Each bullet point must be 1–2 sentences long (clear and direct).
- Do not exceed 5 bullet points per section.
- Keep feedback professional but simple.
- Provide a realistic "score" between 0 and 100.
- Return ONLY JSON, no markdown, no extra commentary.

CV Content:
{CV}
PROMPT;

        $cvSnippet = trim(substr($text, 0, 4000));
        $requestPrompt = str_replace('{CV}', $cvSnippet, $prompt);

        // 5. Call OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a strict CV validator and reviewer.'],
                ['role' => 'user', 'content' => $requestPrompt],
            ],
            'temperature' => 0.0,
            'max_tokens' => 800,
        ]);

        $raw = $response->choices[0]->message->content ?? '';

        // 6. Try to decode JSON
        $json = json_decode($raw, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($json)) {
            return response()->json([
                'success' => false,
                'message' => 'AI returned an invalid response.',
            ], 500);
        }

        // 7. If not a CV, return 422 directly
        if (isset($json['is_cv']) && $json['is_cv'] === false) {
            return response()->json([
                'success' => false,
                'message' => $json['message'] ?? 'This file does not look like a CV.',
            ], 422);
        }

        // 8. Normalize review result
        $result = [
            'strengths'   => array_values(array_slice($json['strengths'] ?? [], 0, 3)),
            'weaknesses'  => array_values(array_slice($json['weaknesses'] ?? [], 0, 3)),
            'suggestions' => array_values(array_slice($json['suggestions'] ?? [], 0, 3)),
            'impression'  => $json['impression'] ?? '',
            'score'       => $json['score'] ?? null,
        ];

        return response()->json([
            'success'   => true,
            'message'   => 'CV uploaded and reviewed successfully.',
            'file_path' => asset('storage/' . $path),
            'file_name' => basename($path),
            'review'    => $result,
        ]);
    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}

}
