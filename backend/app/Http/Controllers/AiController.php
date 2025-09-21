<?php

namespace App\Http\Controllers;

use App\Services\AIReviewService;
use Exception;
use Illuminate\Http\Request;
use OpenAI;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\Facades\Image;
use Stichoza\GoogleTranslate\GoogleTranslate;

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
            $text = $this->aiReview->cleanText($text);
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
    // private function cleanText($text)
    // {
    //     $text = strip_tags($text);
    //     $text = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $text);
    //     $text = preg_replace('/\s+/', ' ', $text);
    //     return trim($text);
    // }
    public function coveletterGenerator(Request $request)
    {
        try {
            info("=== Cover Letter Generation Started ===");

            // =========================
            // 1️⃣ Validate CV input
            // =========================
            info("Validating CV file.");
            $request->validate([
                'cv_file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
            ]);

            $cvFile = $request->file('cv_file');
            info("CV file received: " . $cvFile->getClientOriginalName());

            $cvText = $this->extractTextFromFile($cvFile, 'CV');

            if (empty(trim($cvText))) {
                info("No text extracted from CV.");
                return response()->json([
                    'success' => false,
                    'message' => 'Unable to extract text from the CV. Please upload a clear PDF or image.',
                ], 422);
            }

            info("Cleaned CV text: " . substr($cvText, 0, 200) . "...");

            // =========================
            // 2️⃣ Capture Job Description
            // =========================
            $jobText = null;

            if ($request->filled('job_text')) {
                info("Job text provided in request.");
                $jobText = $this->aiReview->cleanText($request->input('job_text'));
            } elseif ($request->hasFile('job_pdf')) {
                info("Job file uploaded: " . $request->file('job_pdf')->getClientOriginalName());
                $request->validate([
                    'job_pdf' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
                ]);
                $jobFile = $request->file('job_pdf');
                $jobText = $this->extractTextFromFile($jobFile, 'Job');
            } else {
                info("No job details provided.");
                return response()->json([
                    'success' => false,
                    'message' => 'No job details provided. Please paste a job description, provide a link, or upload a PDF/image.',
                ], 422);
            }

            info("Cleaned Job text: " . substr($jobText, 0, 200) . "...");

            // =========================
            // 3️⃣ Generate Cover Letter with OpenAI
            // =========================
            info("Calling OpenAI to generate cover letter.");
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
            info("Cover letter generated successfully.");

            // =========================
            // 4️⃣ Return JSON response
            // =========================
            return response()->json([
                'success' => true,
                'message' => 'Cover letter generated successfully.',
                'cover_letter' => $coverLetter,
            ]);
        } catch (\Exception $e) {
            \Log::error("Unhandled exception in cover letter generation: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract text from PDF or image file (CV or Job) — **without preprocessing**
     */
    private function extractTextFromFile($file, $type = 'File')
    {
        $jobText = '';
        try {
            if ($file->getMimeType() === 'application/pdf') {
                info("$type: Processing PDF.");
                [$filePath, $jobText] = $this->aiReview->extractText($file);
            } else {
                info("$type: Processing image with OCR directly.");
                $ocr = new TesseractOCR($file->getPathname());
                $ocr->lang('eng')->psm(1)->oem(3);
                $jobText = $ocr->run();
                info("$type: OCR completed.");
                $tr = new GoogleTranslate('en'); // target language
                $jobText = $tr->translate($jobText);

                info("Translated Job text: " . substr($jobText, 0, 200) . "...");
            }

            $text = $this->aiReview->cleanText($jobText);
        } catch (\Exception $e) {
            info("$type: Text extraction failed - " . $e->getMessage());
            throw new \Exception("$type text extraction failed: " . $e->getMessage());
        }

        return $text;
    }
}
