<?php

namespace App\Http\Controllers;

use App\Services\AIReviewService;
use Exception;
use Illuminate\Http\Request;
use OpenAI;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Intervention\Image\Facades\Image;
use Stichoza\GoogleTranslate\GoogleTranslate;
use DB;

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


    public function coverLetterGenerator(Request $request)
    {
        try {
            info("=== Coverletter Generatiorn ===");
            $user = auth('api')->user();

            // 1️⃣ Ensure CV exists
            if (!$user->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded. Please upload your CV first.'
                ], 400);
            }

            $cvPath = storage_path('app/public/' . $user->cv_path);
            if (!file_exists($cvPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file not found on server.'
                ], 404);
            }

            // 2️⃣ Extract CV text
            $cvText = $this->extractTextFromFile(new \Illuminate\Http\File($cvPath), 'CV');
            $cvText = $this->trimText($cvText);

            // 3️⃣ Extract job details
            $jobText = $this->getJobText($request);
            if (!$jobText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide job details (paste text, upload a PDF/image, or provide a link).',
                ], 422);
            }
            $jobText = $this->trimText($jobText);

            // 4️⃣ Generate revamped CV
            info("Calling OpenAI for CV revamp...");
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $prompt = "
You are a top-tier Career Consultant and Cover Letter Architect.

Task:
Generate a professional cover letter for the user based on their CV and the provided job description.
- Emphasize skills, experiences, and achievements that are relevant to the job.
- Keep the letter formal, concise, and no longer than 400 words.
- Use a professional but engaging tone; avoid repeating the entire CV.
- Start with a strong opening aligned with the job description.
- Structure the letter with paragraphs (<p>) for readability; do not use unnecessary <div> or <br> tags.
- Only output the **cover letter content**.
- Return ONLY valid JSON with a single field:
  {
    \"revampedCv\": \"<HTML formatted cover letter content here>\"
  }

Original CV:
$cvText

Job Description:
$jobText
";


            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert career coach and CV writer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $revampedCv = $response->choices[0]->message->content ?? '';
            $this->recordUsage($user->id, 'cover_letter', $response->usage->totalTokens ?? 0);

            if (empty($revampedCv)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI returned empty output. Please check the job details.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'Cover letter generated successfully.',
                'revamped_cv' => $revampedCv
            ]);
        } catch (\Exception $e) {
            info("Unhandled exception in cvRevamp: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function emailTemplateGenerator(Request $request)
    {
        try {
            $user = auth('api')->user();

            // 1️⃣ Extract job details
            $jobText = $this->getJobText($request);
            if (!$jobText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide job details (paste text, upload a PDF/image, or provide a link).',
                ], 422);
            }

            $jobText = $this->trimText($jobText);

            // 2️⃣ Generate Email Template
            info("Calling OpenAI to generate job application email template.");
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $prompt = "
You are a professional career assistant.

Generate a professional job application email for the user based on their CV and the provided job description.

### Job Description:
$jobText
### Instructions:
- Emphasize skills, experiences, and achievements relevant to the job.
- Keep the email formal, concise, and no longer than 300 words.
- Use a professional but engaging tone; avoid repeating the entire CV.
- Start with a strong opening aligned with the job description.
- Structure the email using <p> tags for paragraphs for readability; do not use <div> or <br> tags.
- Return ONLY valid JSON with a single field as follows:

{
  \"revampedCv\": \"<HTML formatted email content here>\"
}
";
            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert career coach and email writer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $emailTemplate = trim($response->choices[0]->message->content ?? 'Error generating email template.');
            $this->recordUsage($user->id, 'email_template', $response->usage->totalTokens ?? 0);

            return response()->json([
                'success' => true,
                'message' => 'Job application email template generated successfully.',
                'email_template' => $emailTemplate,
            ]);
        } catch (\Exception $e) {
            info("Unhandled exception in email template generation: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function cvRevamp(Request $request)
    {
        try {
            info("=== CV Revamp Started ===");
            $user = auth('api')->user();

            // 1️⃣ Ensure CV exists
            if (!$user->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded. Please upload your CV first.'
                ], 400);
            }

            $cvPath = storage_path('app/public/' . $user->cv_path);
            if (!file_exists($cvPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file not found on server.'
                ], 404);
            }

            // 2️⃣ Extract CV text
            $cvText = $this->extractTextFromFile(new \Illuminate\Http\File($cvPath), 'CV');
            $cvText = $this->trimText($cvText);

            // 3️⃣ Extract job details
            $jobText = $this->getJobText($request);
            if (!$jobText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide job details (paste text, upload a PDF/image, or provide a link).',
                ], 422);
            }
            $jobText = $this->trimText($jobText);

            // 4️⃣ Generate revamped CV
            info("Calling OpenAI for CV revamp...");
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $prompt = "
You are a top-tier Career Consultant and CV Architect.

Task:
Revamp the user's CV based on the provided job description.
- Prioritize the job description over any conflicting info in the CV.
- Use **bold text** for all section headers (e.g., **Professional Summary**, **Core Skills**).
- Use <p> tags for paragraphs and <ul><li> for lists. Avoid unnecessary <div> or <br> tags.
- Adopt an active, confident, professional tone. Quantify achievements where possible.
- Only output the **revamped CV content**.
- Return ONLY valid JSON with a single field:
  {
    \"revampedCv\": \"<HTML formatted content here>\"
  }
- Do NOT include or reference a cover letter or any other fields.

Original CV:
$cvText

Job Description:
$jobText
";

            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert career coach and CV writer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $revampedCv = $response->choices[0]->message->content ?? '';
            $this->recordUsage($user->id, 'cv_revamp', $response->usage->totalTokens ?? 0);

            if (empty($revampedCv)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI returned empty output. Please check the job details.',
                ], 422);
            }

            return response()->json([
                'success' => true,
                'message' => 'CV revamped successfully.',
                'revamped_cv' => $revampedCv
            ]);
        } catch (\Exception $e) {
            info("Unhandled exception in cvRevamp: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function extractTextFromFile($file, $type = 'File')
    {
        try {
            $mime = $file->getMimeType();
            $text = '';

            if ($mime === 'application/pdf') {
                info("$type: Processing PDF.");
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($file->getPathname());
                $text = $pdf->getText();
            } else {
                info("$type: Processing image with OCR.");
                $ocr = new TesseractOCR($file->getPathname());
                $ocr->lang('eng')->psm(1)->oem(3);
                $text = $ocr->run();
            }
            $text = $this->aiReview->cleanText($text);
            return trim($text);
        } catch (\Exception $e) {
            info("$type: Text extraction failed - " . $e->getMessage());
            throw new \Exception("$type text extraction failed: " . $e->getMessage());
        }
    }
    protected function getJobText(Request $request)
    {
        if ($request->filled('job_text')) {
            info("Job text provided in request.");
            return $this->aiReview->cleanText($request->input('job_text'));
        }

        if ($request->hasFile('job_file')) {
            $this->validateJobFile($request->file('job_file'));
            info("Job file uploaded: " . $request->file('job_file')->getClientOriginalName());
            return $this->extractTextFromFile($request->file('job_file'), 'Job');
        }

        if ($request->filled('job_url')) {
            info("Job URL provided: " . $request->job_url);
            return "Job details can be found here: " . $request->job_url;
        }

        return null;
    }
    protected function validateJobFile($file)
    {
        $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
        if (!in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid file type. Only PDF, JPG, PNG are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('File too large. Maximum allowed size is 5MB.');
        }

        if ($file->getMimeType() === 'application/pdf') {
            $pdf = new \Smalot\PdfParser\Parser();
            $pdfDocument = $pdf->parseFile($file->getPathname());
            if (count($pdfDocument->getPages()) > 3) {
                throw new \Exception('PDF exceeds 3 pages. Please upload a shorter file.');
            }
        }
    }
    protected function trimText($text, $maxLength = 8000)
    {
        // Remove HTML tags and normalize whitespace
        $text = strip_tags($text);
        $text = preg_replace('/\s+/', ' ', $text);
        $text = trim($text);

        // Use mbstring-safe functions
        if (mb_strlen($text, 'UTF-8') > $maxLength) {
            // Cut at the last full stop or space before the limit
            $trimmed = mb_substr($text, 0, $maxLength, 'UTF-8');
            $lastPeriod = mb_strrpos($trimmed, '.', 0, 'UTF-8');
            $lastSpace = mb_strrpos($trimmed, ' ', 0, 'UTF-8');
            $cutPoint = $lastPeriod ?: $lastSpace ?: $maxLength;
            return mb_substr($text, 0, $cutPoint, 'UTF-8') . " ...[truncated]";
        }

        return $text;
    }

    protected function recordUsage($userId, $action, $tokensUsed = 0)
    {
        DB::table('subscriptions')->where('user_id', $userId)->decrement($action === 'cv_revamp' ? 'cv' : 'emails', 1);
        DB::table('usage_activities')->insert([
            'user_id'     => $userId,
            'action'      => $action,
            'status'      => 'success',
            'message'     => ucfirst(str_replace('_', ' ', $action)) . ' Generation',
            'tokens_used' => $tokensUsed,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);
    }
}
