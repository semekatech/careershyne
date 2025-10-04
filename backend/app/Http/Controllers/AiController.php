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
    // private function cleanText($text)
    // {
    //     $text = strip_tags($text);
    //     $text = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $text);
    //     $text = preg_replace('/\s+/', ' ', $text);
    //     return trim($text);
    // }
    public function coverLetterGenerator(Request $request)
    {
        try {
            info("=== Cover Letter Generation Started ===");

            $user = auth('api')->user();
            // =========================
            // 1️⃣ Ensure CV exists
            // =========================
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

            $cvFile = new \Illuminate\Http\File($cvPath);
            $cvText = $this->extractTextFromFile($cvFile, 'CV');
            $cvText = strlen($cvText) > 2000 ? substr($cvText, 0, 2000) . "\n...[truncated]" : $cvText;

            // =========================
            // 2️⃣ Validate Job Input
            // =========================
            $jobText = null;

            if ($request->filled('job_text')) {
                $jobText = $this->aiReview->cleanText($request->input('job_text'));
            } elseif ($request->hasFile('job_file') && $request->file('job_file')->isValid()) {
                $jobFile = $request->file('job_file');

                // File type and size checks
                $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
                if (!in_array($jobFile->getMimeType(), $allowedTypes)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Invalid file type. Only PDF, JPG, PNG are allowed.'
                    ], 422);
                }
                if ($jobFile->getSize() > 5 * 1024 * 1024) {
                    return response()->json([
                        'success' => false,
                        'message' => 'File too large. Maximum allowed size is 5MB.'
                    ], 422);
                }

                if ($jobFile->getMimeType() === 'application/pdf') {
                    $pdf = new \Smalot\PdfParser\Parser();
                    $pdfDocument = $pdf->parseFile($jobFile->getPathname());
                    if (count($pdfDocument->getPages()) > 3) {
                        return response()->json([
                            'success' => false,
                            'message' => 'PDF exceeds 3 pages. Please upload a shorter file.'
                        ], 422);
                    }
                }

                $jobText = $this->extractTextFromFile($jobFile, 'Job');
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No job details provided. Please paste text or upload a PDF/image.'
                ], 422);
            }

            $jobText = strlen($jobText) > 4000 ? substr($jobText, 0, 4000) . "\n...[truncated]" : $jobText;

            if (empty(trim($jobText))) {
                return response()->json([
                    'success' => false,
                    'message' => 'No job description could be extracted from the input.'
                ], 422);
            }

            // =========================
            // 3️⃣ Generate Cover Letter via OpenAI
            // =========================
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $prompt = "
You are a professional career assistant.
Generate a personalized, professional cover letter for this job. **Prioritize the job description over the CV.** Only highlight CV points that strongly match the job.

### CV (for reference only):
$cvText

### Job Description (priority):
$jobText

### Instructions:
- Emphasize skills, experiences, and achievements relevant to the job.
- Keep it formal, concise (max 400 words).
- Use a professional but engaging tone.
- Avoid repeating the entire CV; highlight only what matches the job.
- Start with a strong opening aligned with the job.
";

            $response = $client->chat()->create([
                'model' => 'gpt-4o-mini',
                'messages' => [
                    ['role' => 'system', 'content' => 'You are an expert career coach and cover letter writer.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
            ]);

            $coverLetter = trim($response->choices[0]->message->content ?? '');
            if (empty($coverLetter)) {
                return response()->json([
                    'success' => false,
                    'message' => 'OpenAI returned empty output. Please check the CV and job description.'
                ], 422);
            }


            DB::table('subscriptions')->where('user_id', $user->id)->decrement('coverletters', 1);
            DB::table('usage_activities')->insert([
                'user_id'     => $user->id,
                'action'      => 'cover_letter',
                'status'      => 'success',
                'message'     => 'Cover Letter Generation.',
                'tokens_used' => $response->usage->totalTokens ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            // =========================
            // 5️⃣ Return JSON response
            // =========================
            return response()->json([
                'success' => true,
                'message' => 'Cover letter generated successfully.',
                'cover_letter' => $coverLetter,
            ]);
        } catch (\Exception $e) {
            info("Unhandled exception in coverLetterGenerator: " . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
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
Generate a job application email template tailored to the provided job description.

### Job Description:
$jobText

### Instructions:
- Write a formal job application email (max 300 words).
- Structure it with: greeting, short introduction, why the applicant is a good fit, polite closing.
- Use a professional but engaging tone.
- Do not include placeholders like [Name] or [Company] unless necessary.
- The email should sound natural and ready to send.
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
    protected function trimText($text, $maxLength = 4000)
    {
        return strlen($text) > $maxLength
            ? substr($text, 0, $maxLength) . "\n...[truncated]"
            : $text;
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
