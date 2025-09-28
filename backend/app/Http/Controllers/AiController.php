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
            info(json_encode($response, JSON_PRETTY_PRINT));
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
    public function emailTemplateGenerator(Request $request)
    {
        try {
            info("Job Text: " . $request->input('job_text'));
            info("Job URL: " . $request->input('job_url'));
            info("Has job_pdf? " . ($request->hasFile('job_pdf') ? 'yes' : 'no'));

            info("=== Job Application Email Template Generation Started ===");
            // =========================
            // 1️⃣ Capture Job Description
            // =========================
            $jobText = null;

            if ($request->filled('job_text')) {
                info("Job text provided in request.");
                $jobText = $this->aiReview->cleanText($request->input('job_text'));
            } elseif ($request->hasFile('job_pdf')) {
                info("Job PDF uploaded: " . $request->file('job_pdf')->getClientOriginalName());
                $request->validate([
                    'job_pdf' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
                ]);
                $jobFile = $request->file('job_pdf');
                $jobText = $this->extractTextFromFile($jobFile, 'Job');
            } elseif ($request->filled('job_url')) {
                info("Job URL provided: " . $request->job_url);
                $jobText = "Job details can be found here: " . $request->job_url;
            } else {
                info("No job details provided.");
                return response()->json([
                    'success' => false,
                    'message' => 'Please provide job details (paste text, upload a PDF/image, or provide a link).',
                ], 422);
            }

            info("Cleaned Job text: " . substr($jobText, 0, 200) . "...");

            // =========================
            // 2️⃣ Generate Email Template with OpenAI
            // =========================
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
            info(json_encode($response, JSON_PRETTY_PRINT));
            $emailTemplate = trim($response->choices[0]->message->content ?? 'Error generating email template.');
            info("Email template generated successfully.");

            // =========================
            // 3️⃣ Return JSON response
            // =========================
            return response()->json([
                'success' => true,
                'message' => 'Job application email template generated successfully.',
                'email_template' => $emailTemplate,
            ]);
        } catch (\Exception $e) {
            \Log::error("Unhandled exception in email template generation: " . $e->getMessage());
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

        // Extract text from stored CV and trim to prevent abuse
        $cvText = $this->extractTextFromFile(new \Illuminate\Http\File($cvPath), 'CV');
        $maxCvLength = 4000;
        if (strlen($cvText) > $maxCvLength) {
            $cvText = substr($cvText, 0, $maxCvLength) . "\n...[truncated]";
        }

        // 2️⃣ Validate uploaded job file (PDF or image)
       if (!$request->hasFile('job_file') || !$request->file('job_file')->isValid()) {
    $error = $request->file('job_file') ? $request->file('job_file')->getError() : 'No file detected';
    return response()->json([
        'success' => false,
        'message' => "Job file is required or invalid. PHP upload error code: $error"
    ], 422);
}


        $jobFile = $request->file('job_file');

        // File type check
        $allowedTypes = ['application/pdf', 'image/png', 'image/jpeg', 'image/jpg'];
        if (!in_array($jobFile->getMimeType(), $allowedTypes)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file type. Only PDF, JPG, PNG are allowed.'
            ], 422);
        }

        // File size check (5MB)
        if ($jobFile->getSize() > 5 * 1024 * 1024) {
            return response()->json([
                'success' => false,
                'message' => 'File too large. Maximum allowed size is 5MB.'
            ], 422);
        }

        // PDF page limit check (3 pages max)
        if ($jobFile->getMimeType() === 'application/pdf') {
            $pdf = new \Smalot\PdfParser\Parser();
            $pdfDocument = $pdf->parseFile($jobFile->getPathname());
            $pageCount = count($pdfDocument->getPages());
            if ($pageCount > 3) {
                return response()->json([
                    'success' => false,
                    'message' => 'PDF exceeds 3 pages. Please upload a shorter file.'
                ], 422);
            }
        }

        // 3️⃣ Extract job description text and trim
        $jobText = $this->extractTextFromFile($jobFile, 'Job');
        $maxJobLength = 4000;
        if (strlen($jobText) > $maxJobLength) {
            $jobText = substr($jobText, 0, $maxJobLength) . "\n...[truncated]";
        }

        if (empty($jobText)) {
            return response()->json([
                'success' => false,
                'message' => 'No job description could be extracted from the file.'
            ], 422);
        }

        // 4️⃣ Build prompt for OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));

        $prompt = "
You are a top-tier Career Consultant and CV Architect.

Task:
Revamp the user's CV based on the provided job description.
- Prioritize the job description over any conflicting info in the CV.
- Use **bold text** for all section headers (e.g., **Professional Summary**, **Core Skills**).
- Adopt an active, confident, professional tone. Quantify achievements where possible.
- Only output the **revamped CV content**. Do NOT include any summaries, filler text, or 'Additional Information'.

Original CV:
$cvText

Job Description:
$jobText
";

        // 5️⃣ Send to OpenAI
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are an expert career coach and CV writer.'],
                ['role' => 'user', 'content' => $prompt],
            ],
        ]);

        $revampedCv = $response->choices[0]->message->content ?? '';

        if (empty($revampedCv)) {
            return response()->json([
                'success' => false,
                'message' => 'OpenAI returned empty output. Please check the job file or text.',
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


    // private function extractTextFromFile($file, $type = 'File')
    // {
    //     $jobText = '';
    //     try {
    //         if ($file->getMimeType() === 'application/pdf') {
    //             info("$type: Processing PDF.");
    //             [$filePath, $jobText] = $this->aiReview->extractText($file);
    //         } else {
    //             info("$type: Processing image with OCR directly.");
    //             $ocr = new TesseractOCR($file->getPathname());
    //             $ocr->lang('eng')->psm(1)->oem(3);
    //             $jobText = $ocr->run();
    //             info("$type: OCR completed.");
    //             $tr = new GoogleTranslate('en');
    //             $jobText = $tr->translate($jobText);

    //             info("Translated Job text: " . substr($jobText, 0, 200) . "...");
    //         }

    //         $text = $this->aiReview->cleanText($jobText);
    //     } catch (\Exception $e) {
    //         info("$type: Text extraction failed - " . $e->getMessage());
    //         throw new \Exception("$type text extraction failed: " . $e->getMessage());
    //     }

    //     return $text;
    // }


    //      public function cvRevamp(Request $request)
    //     {
    //         try {
    //             info("=== CV Revamp Started ===");

    //             // 1️⃣ Validate CV file
    //             $request->validate([
    //                 'cv_file' => 'required|mimes:pdf,jpg,jpeg,png|max:5120',
    //             ]);

    //             $cvFile = $request->file('cv_file');
    //             $cvText = $this->extractTextFromFile($cvFile, 'CV');

    //             if (empty(trim($cvText))) {
    //                 return response()->json([
    //                     'success' => false,
    //                     'message' => 'Unable to extract text from the CV.',
    //                 ], 422);
    //             }

    //             // 2️⃣ Capture optional Job Details
    //             $jobText = null;

    //             if ($request->filled('job_text')) {
    //                 $jobText = $this->aiReview->cleanText($request->input('job_text'));
    //             } elseif ($request->hasFile('job_pdf')) {
    //                 $request->validate([
    //                     'job_pdf' => 'mimes:pdf,jpg,jpeg,png|max:5120',
    //                 ]);
    //                 $jobFile = $request->file('job_pdf');
    //                 $jobText = $this->extractTextFromFile($jobFile, 'Job');
    //             } elseif ($request->filled('job_url')) {
    //                 $jobText = "Job details can be found here: " . $request->job_url;
    //             }

    //             // 3️⃣ Generate Revamped CV with OpenAI
    //             $client = OpenAI::client(env('OPENAI_API_KEY'));

    //             $prompt = "
    // You are a professional career consultant.
    // Revamp the following CV to make it professional, clear, and impactful.

    // ### CV:
    // $cvText

    // ### Instructions:
    // - ONLY return the improved CV.
    // - Do NOT include a cover letter, greeting, or application email.
    // - Keep the structure of the CV clean and professional.
    // ";


    //             if ($jobText) {
    //                 $prompt .= "

    // ### Job Description:
    // $jobText

    // ### Instructions:
    // - Tailor the CV to highlight the applicant’s skills and experiences that match this job.
    // - Use keywords from the job description naturally.
    // ";
    //             } else {
    //                 $prompt .= "

    // ### Instructions:
    // - Improve clarity, grammar, and formatting.
    // - Make the CV stand out for general job applications.
    // ";
    //             }

    //             $response = $client->chat()->create([
    //                 'model' => 'gpt-4o-mini',
    //                 'messages' => [
    //                     ['role' => 'system', 'content' => 'You are an expert career coach and CV writer.'],
    //                     ['role' => 'user', 'content' => $prompt],
    //                 ],
    //             ]);
    //          info(json_encode($response, JSON_PRETTY_PRINT));
    //             $revampedCV = trim($response->choices[0]->message->content ?? 'Error revamping CV.');

    //             // 4️⃣ Return response
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'CV revamped successfully.',
    //                 'revamped_cv' => $revampedCV,
    //             ]);
    //         } catch (\Exception $e) {
    //             \Log::error("Unhandled exception in CV revamp: " . $e->getMessage());
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Error: ' . $e->getMessage(),
    //             ], 500);
    //         }
    //     }

}
