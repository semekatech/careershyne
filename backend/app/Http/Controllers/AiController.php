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
use OpenAI;
use Illuminate\Support\Facades\Http;
class AiController extends Controller
{

    public function uploadCV(Request $request)
    {
               // ❗ 1. Validate reCAPTCHA Token
        $recaptchaToken = $request->input('recaptchaToken');
        if (empty($recaptchaToken)) {
            return response()->json(['error' => 'reCAPTCHA token is missing.'], 400);
        }

        // ❗ ⚠️ Replace with your actual SECRET KEY
        $recaptchaSecretKey = "6LfQzc0rAAAAAIfZscdxvLfxy6wMPctZrtRtdgW1";

        try {
            // Send a POST request to Google's verification API
            $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                'secret' => $recaptchaSecretKey,
                'response' => $recaptchaToken,
                'remoteip' => $request->ip(),
            ]);

            $result = $response->json();

            // Check if the verification was successful and the score is acceptable
            if (!$result['success'] || $result['score'] < 0.5) { // ❗ Adjust the score threshold as needed
                info('reCAPTCHA failed: ' . json_encode($result));
                return response()->json(['error' => 'reCAPTCHA verification failed. Please try again.'], 401);
            }
        } catch (\Exception $e) {
            info('reCAPTCHA verification error: ' . $e->getMessage());
            return response()->json(['error' => 'Server error during reCAPTCHA verification.'], 500);
        }

        // ✅ 1. Validate input
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120',
        ]);

        // ✅ 2. Store file
        $path = $request->file('file')->store('cvs', 'public');
        info('uploaded');

        // ✅ 3. Extract text from PDF
        $parser = new Parser();
        $pdf = $parser->parseFile($request->file('file')->getPathname());
        $text = $pdf->getText();

        // ✅ 4. Call OpenAI to review CV
    //     $client = OpenAI::client(env('OPENAI_API_KEY'));

    //     $prompt = "
    // You are a professional career coach. Review the following CV text and provide:
    // - Strengths
    // - Weaknesses
    // - Suggestions for improvement
    // - Overall impression

    // CV Content:
    // " . substr($text, 0, 4000); // truncate to avoid token limits

    //     $response = $client->chat()->create([
    //         'model' => 'gpt-4o-mini',
    //         'messages' => [
    //             ['role' => 'system', 'content' => 'You are a professional CV reviewer.'],
    //             ['role' => 'user', 'content' => $prompt],
    //         ],
    //     ]);

    //     $review = $response->choices[0]->message->content;
    //     info($review);
        // ✅ 5. Return response
        return response()->json([
            'success' => true,
            'message' => 'CV uploaded and reviewed successfully.',
            'file_path' => asset('storage/' . $path),
            'file_name' => basename($path),
            // 'review'    => $review,
        ]);
    }



}


