<?php
namespace App\Http\Controllers;

use App\Services\AIReviewService;
use DB;
use Exception;
use Illuminate\Http\Request;
use OpenAI;
use thiagoalessio\TesseractOCR\TesseractOCR;

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
            $file   = $request->file('file');
            $handle = fopen($file->getPathname(), 'rb');
            $magic  = fread($handle, 4);
            fclose($handle);

            if ($magic !== '%PDF') {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid file format, only genuine PDFs allowed.',
                ], 422);
            }

            // 2. Extract text
            [$path, $text] = $this->aiReview->extractText($request->file('file'));
            $text          = $this->aiReview->cleanText($text);
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
            info("=== Cover Letter Generation ===");
            $user = auth('api')->user();

            // 1️⃣ Ensure CV exists
            if (! $user->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded. Please upload your CV first.',
                ], 400);
            }

            $cvPath = storage_path('app/public/' . $user->cv_path);
            if (! file_exists($cvPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file not found on server.',
                ], 404);
            }

            $cvText = '';

            // -------------------------
            // 2️⃣ Try Smalot PDF Parser
            // -------------------------
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($cvPath);
                $cvText = $pdf->getText() ?? '';
            } catch (\Throwable $e) {
                info("Smalot parser failed for: $cvPath — " . $e->getMessage());
                $cvText = '';
            }

            // -------------------------
            // 3️⃣ Fallback to OCR if empty
            // -------------------------
            if (strlen(trim($cvText)) === 0) {
                try {
                    info("CV parsing returned empty text. Trying OCR... File: $cvPath");

                    $imagick = new \Imagick();
                    $imagick->setResolution(300, 300);
                    $imagick->readImage($cvPath);
                    $imagick->setImageFormat('png');

                    $ocrText = '';

                    foreach ($imagick as $index => $page) {
                        $tmpImg = tempnam(sys_get_temp_dir(), 'ocr_') . '.png';
                        $page->writeImage($tmpImg);

                        $tmpOutput = tempnam(sys_get_temp_dir(), 'txt_');
                        $command   = sprintf('tesseract %s %s -l eng 2>&1', escapeshellarg($tmpImg), escapeshellarg($tmpOutput));
                        exec($command, $cmdOutput, $status);

                        if ($status === 0 && file_exists($tmpOutput . '.txt')) {
                            $ocrText .= file_get_contents($tmpOutput . '.txt') . "\n";
                            unlink($tmpOutput . '.txt');
                        } else {
                            info("OCR page {$index} failed for {$cvPath}. Output: " . implode("\n", $cmdOutput));
                        }

                        unlink($tmpImg);
                    }

                    $imagick->clear();
                    $imagick->destroy();

                    $cvText = trim($ocrText);

                    if (strlen($cvText) === 0) {
                        info("OCR also returned empty text for file: $cvPath");
                    }
                } catch (\Throwable $ocrError) {
                    info("OCR failed for $cvPath — " . $ocrError->getMessage());
                    $cvText = '';
                }
            }

            if (strlen(trim($cvText)) === 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read CV. Please upload a valid, text-based or scanned PDF file.',
                ], 422);
            }

            // -------------------------
            // 4️⃣ Clean CV text
            // -------------------------
            $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
            $cvText = preg_replace('/\s+/', ' ', $cvText);
            $cvText = trim($cvText);

            // -------------------------
            // 5️⃣ Get job info
            // -------------------------
            $job = Job::findOrFail($request->jobId);

            // -------------------------
            // 6️⃣ Build AI Prompt
            // -------------------------
            $prompt = <<<PROMPT
You are a top-tier Career Consultant and Cover Letter Architect.

Task:
Generate a professional, customized **cover letter** for this candidate, based on their CV and the provided job details.

Requirements:
- Emphasize the candidate’s relevant experience, achievements, and skills that align with the job.
- Use a professional and engaging tone — formal but warm.
- Keep it concise (250–400 words).
- Structure it properly with HTML <p> tags, using clear paragraphs.
- Avoid repeating full CV content; focus on personalization and fit.
- Start with a compelling introduction and end with a polite closing.
- Do NOT fabricate details — use only information inferred from the CV.
- Return ONLY valid JSON with this field:
  - "revampedCV" (HTML formatted)

--- PERSONAL DETAILS ---
Name: {$user->name}
Email: {$user->email}
Phone: {$user->phone}

--- HIRING COMPANY ---
{$job->company}

--- JOB TITLE ---
{$job->title}

--- JOB DESCRIPTION ---
{$job->description}

--- ORIGINAL CV TEXT ---
{$cvText}
PROMPT;

            // -------------------------
            // 7️⃣ Call OpenAI
            // -------------------------
            $client   = OpenAI::client(env('OPENAI_API_KEY'));
            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are an expert cover letter writing assistant. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            $aiOutput = $response->choices[0]->message->content ?? '';

            $cleanOutput = preg_replace('/^```(json)?/m', '', $aiOutput);
            $cleanOutput = preg_replace('/```$/m', '', $cleanOutput);
            $cleanOutput = trim($cleanOutput);

            $analysis = json_decode($cleanOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! $analysis) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI response could not be parsed',
                    'raw'     => $aiOutput,
                ], 500);
            }

            // -------------------------
            // 8️⃣ Log usage
            // -------------------------
            DB::table('subscriptions')->where('user_id', $user->id)->decrement('cover_letters', 1);
            DB::table('usage_activities')->insert([
                'user_id'     => $user->id,
                'action'      => 'cover_letter',
                'status'      => 'success',
                'message'     => 'Cover Letter Generation',
                'tokens_used' => $response->usage->totalTokens ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // -------------------------
            // 9️⃣ Return Response
            // -------------------------
            return response()->json([
                'success'     => true,
                'coverLetter' => $analysis['coverLetter'] ?? '',
                'cv_path'     => asset('storage/' . $user->cv_path),
            ]);
        } catch (\Throwable $e) {
            info("Cover letter generation failed: " . $e->getMessage());

            DB::table('usage_activities')->insert([
                'user_id'    => $user->id ?? null,
                'action'     => 'cover_letter',
                'status'     => 'failed',
                'message'    => $e->getMessage(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Error generating cover letter: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function emailTemplateGenerator(Request $request)
    {
        try {
            info("=== Job Application Email Generation ===");

            $user = auth('api')->user();

            // 1️⃣ Ensure CV exists
            if (! $user->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded. Please upload your CV first.',
                ], 400);
            }

            $cvPath = storage_path('app/public/' . $user->cv_path);
            if (! file_exists($cvPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file not found on server.',
                ], 404);
            }

            // 2️⃣ Extract CV text (Smalot + OCR fallback)
            $cvText = $this->extractTextFromFile(new \Illuminate\Http\File($cvPath), 'CV');
            $cvText = $this->trimText($cvText);

            // 3️⃣ Extract job details (paste text, file, or link)
            $jobText = $this->getJobText($request);
            if (! $jobText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide job details (paste text, upload a PDF/image, or provide a link).',
                ], 422);
            }

            $jobText = $this->trimText($jobText);

            // 4️⃣ Build AI Prompt
            $prompt = <<<PROMPT
You are a professional career consultant and email writing expert.

Task:
Generate a professional **job application email** for the user based on their CV and the provided job description.

### Personal Information:
Name: {$user->name}
Email: {$user->email}
Phone: {$user->phone}

### Job Description:
$jobText

### CV Text:
$cvText

### Instructions:
- Keep the email concise (max 300 words).
- Use a formal yet engaging tone suitable for job applications.
- Emphasize key achievements, skills, and relevance to the role.
- Start with a strong, attention-grabbing introduction.
- Avoid restating the entire CV — focus on alignment with the job.
- Structure the email using <p> tags for paragraphs (no <div> or <br> tags).
- End politely with a call-to-action (like “Looking forward to your response”).
- Return ONLY valid JSON in this exact format:

{
  "emailTemplate": "<HTML formatted email body here>"
}
PROMPT;

            // 5️⃣ Call OpenAI API
            info("Calling OpenAI to generate job application email template...");
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are an expert job application email writer. Always return valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.4,
            ]);

            // 6️⃣ Clean & parse output
            $aiOutput    = $response->choices[0]->message->content ?? '';
            $cleanOutput = preg_replace('/^```(json)?/m', '', $aiOutput);
            $cleanOutput = preg_replace('/```$/m', '', $cleanOutput);
            $cleanOutput = trim($cleanOutput);

            $decoded = json_decode($cleanOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! isset($decoded['emailTemplate'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse AI response.',
                    'raw'     => $aiOutput,
                ], 500);
            }

            // 7️⃣ Record usage
            $this->recordUsage($user->id, 'email_template', $response->usage->totalTokens ?? 0);

            // 8️⃣ Return final response
            return response()->json([
                'success'        => true,
                'message'        => 'Job application email generated successfully.',
                'email_template' => $decoded['emailTemplate'],
            ]);

        } catch (\Exception $e) {
            info("Unhandled exception in emailTemplateGenerator: " . $e->getMessage());
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
            if (! $user->cv_path) {
                return response()->json([
                    'success' => false,
                    'message' => 'No CV uploaded. Please upload your CV first.',
                ], 400);
            }

            $cvPath = storage_path('app/public/' . $user->cv_path);
            if (! file_exists($cvPath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'CV file not found on server.',
                ], 404);
            }

            // 2️⃣ Extract CV text (Smalot + OCR fallback)
            $cvText = $this->extractTextFromFile(new \Illuminate\Http\File($cvPath), 'CV');
            $cvText = $this->trimText($cvText);

            // 3️⃣ Extract job details
            $jobText = $this->getJobText($request);
            if (! $jobText) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide job details (paste text, upload a PDF/image, or provide a link).',
                ], 422);
            }

            $jobText = $this->trimText($jobText);

            // 4️⃣ Prepare AI prompt
            $prompt = <<<PROMPT
You are a world-class Career Consultant and CV Branding Expert.

Task:
Revamp the user's CV based on their original content and the provided job description.

### Candidate Information:
Name: {$user->name}
Email: {$user->email}
Phone: {$user->phone}

### Job Description:
$jobText

### Original CV Text:
$cvText

### Guidelines:
- Prioritize the job description when tailoring content.
- Emphasize achievements, skills, and experience relevant to the job.
- Keep the tone professional, confident, and results-driven.
- Use **bold section headers** (e.g., **Professional Summary**, **Core Skills**, **Experience**, **Education**).
- Structure the document using valid HTML tags: <h2>, <p>, <ul>, <li>.
- Avoid unnecessary <div> or <br> tags.
- If any section data is missing, omit that section gracefully.
- Output ONLY valid JSON in this format:

{
  "revampedCv": "<HTML formatted revamped CV content here>"
}
PROMPT;

            // 5️⃣ Call OpenAI API
            info("Calling OpenAI for CV revamp...");
            $client = OpenAI::client(env('OPENAI_API_KEY'));

            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are an expert CV writer and career consultant. Always return valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            // 6️⃣ Parse AI output
            $aiOutput    = $response->choices[0]->message->content ?? '';
            $cleanOutput = preg_replace('/^```(json)?/m', '', $aiOutput);
            $cleanOutput = preg_replace('/```$/m', '', $cleanOutput);
            $cleanOutput = trim($cleanOutput);

            $decoded = json_decode($cleanOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! isset($decoded['revampedCv'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse AI response.',
                    'raw'     => $aiOutput,
                ], 500);
            }

            // 7️⃣ Record usage and decrement CV credits
            DB::table('subscriptions')->where('user_id', $user->id)->decrement('cv', 1);
            $this->recordUsage($user->id, 'cv_revamp', $response->usage->totalTokens ?? 0);

            // 8️⃣ Return response
            return response()->json([
                'success'     => true,
                'message'     => 'CV revamped successfully.',
                'revamped_cv' => $decoded['revampedCv'],
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
                $pdf    = $parser->parseFile($file->getPathname());
                $text   = $pdf->getText();
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
        if (! in_array($file->getMimeType(), $allowedTypes)) {
            throw new \Exception('Invalid file type. Only PDF, JPG, PNG are allowed.');
        }

        if ($file->getSize() > 5 * 1024 * 1024) {
            throw new \Exception('File too large. Maximum allowed size is 5MB.');
        }

        if ($file->getMimeType() === 'application/pdf') {
            $pdf         = new \Smalot\PdfParser\Parser();
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
            $trimmed    = mb_substr($text, 0, $maxLength, 'UTF-8');
            $lastPeriod = mb_strrpos($trimmed, '.', 0, 'UTF-8');
            $lastSpace  = mb_strrpos($trimmed, ' ', 0, 'UTF-8');
            $cutPoint   = $lastPeriod ?: $lastSpace ?: $maxLength;
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
