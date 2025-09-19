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
        // ✅ 1. Validate input
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        // ✅ 2. Store file
        $path = $request->file('file')->store('cvs', 'public');
        info('File uploaded: ' . $path);

        // ✅ 3. Extract text from PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($request->file('file')->getPathname());
        $text = $pdf->getText() ?? '';

        if (empty(trim($text))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to extract text from the PDF. Please upload a text-based CV.',
            ], 422);
        }

        // ✅ 4. Call OpenAI for review
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $prompt = "
        You are a professional career coach. Review the following CV text and provide:
        - Strengths
        - Weaknesses
        - Suggestions for improvement
        - Overall impression

        CV Content:
        " . substr($text, 0, 4000); // truncate to avoid token limits

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a professional CV reviewer.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $review = $response->choices[0]->message->content ?? 'No review generated.';

        // ✅ 5. Return success
        return response()->json([
            'success'   => true,
            'message'   => 'CV uploaded and reviewed successfully.',
            'file_path' => asset('storage/' . $path),
            'file_name' => basename($path),
            'review'    => $review,
        ]);

    } catch (Exception $e) {
        // ✅ Catch and return any error
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}
}
