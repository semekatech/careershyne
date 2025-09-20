<?php

namespace App\Http\Controllers;

use App\Services\AIReviewService;
use Exception;
use Illuminate\Http\Request;
use OpenAI;

class AiController extends Controller
{
    protected $aiReview;

    public function __construct(AIReviewService $aiReview)
    {
        $this->aiReview = $aiReview;
    }

    public function uploadCV(Request $request)
    {
        try {
            $count = \DB::table('ai_reviews')
                ->whereDate('created_at', now()->toDateString())
                ->count();

            if ($count >= 100) {
                return response()->json([
                    'success' => false,
                    'message' => 'Daily limit reached for Careershyne AI. Please try again tomorrow.',
                ], 422);
            }
            // 1. Validate
            $request->validate([
                'file' => 'required|mimetypes:application/pdf|max:5120',
            ]);
            $file = $request->file('file');
            $handle = fopen($file->getPathname(), 'rb');
            $magic = fread($handle, 4);
            fclose($handle);

            if ($magic !== '%PDF') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file format, only genuine PDFs allowed.'
                ], 422);
            }

            // 2. Extract text
            [$path, $text] = $this->aiReview->extractText($request->file('file'));

            if (empty(trim($text))) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to extract text from the PDF. Please upload a text-based CV.',
                ], 422);
            }

            // 3. Analyze with AI
            $json = $this->aiReview->analyzeCV($text);
            if (isset($json['is_cv']) && $json['is_cv'] === false) {
                return response()->json([
                    'success' => false,
                    'message' => $json['message'] ?? 'This file does not look like a CV.',
                ], 422);
            }

            // 4. Normalize
            $result = $this->aiReview->normalizeResult($json);

            // 5. Save
            $this->aiReview->saveReview($json, $path, $result);

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
   private function cleanText($text)
{
    $text = strip_tags($text);
    $text = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $text);
    $text = preg_replace('/\s+/', ' ', $text);
    return trim($text);
}

public function coveletterGenerator(Request $request)
{
    try {
        // 1. Validate CV
        $request->validate([
            'cv_file' => 'required|mimetypes:application/pdf,application/vnd.openxmlformats-officedocument.wordprocessingml.document|max:5120',
        ]);

        $cvFile = $request->file('cv_file');
        if ($cvFile->getMimeType() === 'application/pdf') {
            $handle = fopen($cvFile->getPathname(), 'rb');
            $magic = fread($handle, 4);
            fclose($handle);
            if ($magic !== '%PDF') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid CV file format. Please upload a genuine PDF.'
                ], 422);
            }
        }

        [$cvFilePath, $cvText] = $this->aiReview->extractText($cvFile);
        $cvText = $this->cleanText($cvText);

        if (empty(trim($cvText))) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to extract text from the CV. Please upload a text-based file.',
            ], 422);
        }

        // 2. Capture Job Description
        $jobText = null;
        if ($request->filled('job_text')) {
            $jobText = $this->cleanText($request->input('job_text'));
        } elseif ($request->has('job_url') && !empty(trim($request->input('job_url')))) {
            $jobUrl = $request->input('job_url');
            // Scraper placeholder
            $jobText = "Scraped job description from URL: " . $jobUrl;
            $jobText = $this->cleanText($jobText);
        } elseif ($request->hasFile('job_pdf')) {
            $request->validate([
                'job_pdf' => 'required|mimetypes:application/pdf|max:5120',
            ]);
            $jobPdfFile = $request->file('job_pdf');
            $handle = fopen($jobPdfFile->getPathname(), 'rb');
            $magic = fread($handle, 4);
            fclose($handle);
            if ($magic !== '%PDF') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid job PDF file format. Please upload a genuine PDF.'
                ], 422);
            }
            [$jobFilePath, $jobText] = $this->aiReview->extractText($jobPdfFile);
            $jobText = $this->cleanText($jobText);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No job details provided. Please paste a job description, provide a link, or upload a PDF.',
            ], 422);
        }

        // 3. Pass to AI
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $prompt = "
You are a professional career assistant.
Generate a personalized, professional cover letter using the applicant’s CV and the provided job description.

### CV:
$cvText

### Job Description:
$jobText

### Instructions:
- Keep the cover letter formal and concise (max 400 words).
- Emphasize the applicant’s skills and experiences that match the job requirements.
- Use a professional but engaging tone.
- Do not repeat the CV; instead, highlight relevant points.
";

        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert career coach and writer.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $coverLetter = trim($response->choices[0]->message->content ?? 'Error generating cover letter.');

        // 4. Return JSON response
        return response()->json([
            'success' => true,
            'message' => 'Cover letter generated successfully.',
            'cover_letter' => $coverLetter,
        ]);

    } catch (Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage(),
        ], 500);
    }
}

}
