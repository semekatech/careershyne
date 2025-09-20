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

            // 4. Build strict JSON-only prompt
            $prompt = <<<PROMPT
As a highly sophisticated and keen human resource professional, critically review the provided CV with
the intention of educating the CV holder by pointing out areas of strengths in their CV and areas of
improvements that need to be addressed to help them stand out among recruiters in their field.
Read the provided CV content and return a single JSON object (no extra text) with this exact schema:

{
  "strengths": ["bullet 1", "bullet 2"],
  "weaknesses": ["bullet 1", "bullet 2"],
  "suggestions": ["bullet 1", "bullet 2"],
  "impression": "summarize overall impression.",
  "score": 0
}

Requirements:
- Each bullet point must be 1–2 sentences long (clear and direct).
- Do not exceed 5 bullet points per section.
- Keep the feedback professional but easy to understand.
- Provide a realistic "score" between 0 and 100 based on the balance of strengths and weaknesses.
- Return ONLY the JSON object. Do not include any Markdown, headings, or additional commentary.

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
                    ['role' => 'system', 'content' => 'You are a professional CV reviewer.'],
                    ['role' => 'user', 'content' => $requestPrompt],
                ],
                'temperature' => 0.0, // deterministic output
                'max_tokens' => 800,
            ]);

            $raw = $response->choices[0]->message->content ?? '';

            // 6. Extract JSON from raw response robustly
            $json = null;
            // try direct decode
            $decoded = json_decode($raw, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $json = $decoded;
            } else {
                // try to extract the first {...} block
                if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $raw, $m)) {
                    $maybe = $m[0];
                    $decoded = json_decode($maybe, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $json = $decoded;
                    }
                }
            }

            // 7. If parsing failed, create a safe fallback: parse heuristically into arrays & score
            if (!is_array($json)) {
                // fallback: place raw text under impression and attempt to extract Score:
                preg_match('/"score"\s*:\s*(\d{1,3})|Score:\s*(\d{1,3})/i', $raw, $mscore);
                $score = isset($mscore[1]) && is_numeric($mscore[1]) ? (int)$mscore[1] : null;

                $json = [
                    'strengths'   => [],
                    'weaknesses'  => [],
                    'suggestions' => [],
                    'impression'  => substr(trim(strip_tags($raw)), 0, 400),
                    'score'       => $score,
                    'raw'         => $raw,
                ];
            }

            // Normalize to ensure keys exist and types are correct
            $result = [
                'strengths'   => array_values(array_slice(array_map('trim', $json['strengths'] ?? []), 0, 3)),
                'weaknesses'  => array_values(array_slice(array_map('trim', $json['weaknesses'] ?? []), 0, 3)),
                'suggestions' => array_values(array_slice(array_map('trim', $json['suggestions'] ?? []), 0, 3)),
                'impression'  => isset($json['impression']) ? trim($json['impression']) : '',
                'score'       => isset($json['score']) ? (int)$json['score'] : null,
                'raw'         => $raw,
            ];

            return response()->json([
                'success'   => true,
                'message'   => 'CV uploaded and reviewed successfully.',
                'file_path' => asset('storage/' . $path),
                'file_name' => basename($path),
                'review'    => $result, // structured data
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }
}
