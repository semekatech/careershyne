<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Services\AIReviewService;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use OpenAI;
use Smalot\PdfParser\Parser;

class JobController extends Controller
{
    protected $aiReview;

    public function __construct(AIReviewService $aiReview)
    {
        $this->aiReview = $aiReview;
    }
    public function store(Request $request)
    {
        // ✅ Validate incoming request
        $validated = $request->validate([
            'company'                 => 'required|string|max:255',
            'title'                   => 'required|string|max:255',
            'type'                    => 'required|string',
            'experience'              => 'required|integer|min:0',
            'education'               => 'required|string|max:255',
            'salary'                  => 'required|string|max:255',
            'deadline'                => 'required|date',
            'field'                   => 'required|string|max:255',
            'description'             => 'required|string',
            'applicationInstructions' => 'required|string',
            'country'                 => 'required|string|max:255',
            'county'                  => 'required|string|max:255',
            'office'                  => 'required|string|max:255',
        ]);
        $validated['posted_by'] = auth('api')->id();
        $validated['slug']      = Str::slug($validated['title'] . '-' . time());
        $job                    = Job::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Job created successfully',
            'data'    => $job,
        ], 201);
    }
    public function fetchAll(Request $request)
    {
        $query = Job::query()
            ->leftJoin('industries', 'industries.id', '=', 'job_listings.field')
            ->select('job_listings.*', 'industries.name as field_name');

        // Optional search
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_listings.title', 'like', "%{$search}%")
                    ->orWhere('job_listings.company', 'like', "%{$search}%")
                    ->orWhere('job_listings.county', 'like', "%{$search}%")
                    ->orWhere('job_listings.country', 'like', "%{$search}%")
                    ->orWhere('job_listings.name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 100);
        $jobs    = $query->orderBy('job_listings.created_at', 'desc')->paginate($perPage);

        return response()->json($jobs);
    }

    public function userJobs(Request $request)
    {
        $query = Job::from('job_listings as job_listings')
            ->leftJoin('industries', 'industries.id', '=', 'job_listings.field_name')
            ->select('job_listings.*', 'industries.name as field');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_listings.title', 'like', "%{$search}%")
                    ->orWhere('job_listings.company', 'like', "%{$search}%")
                    ->orWhere('job_listings.county', 'like', "%{$search}%")
                    ->orWhere('job_listings.country', 'like', "%{$search}%")
                    ->orWhere('industries.name', 'like', "%{$search}%");
            });
        }

        $perPage = (int) $request->get('per_page', 10);
        $jobs    = $query->orderBy('job_listings.created_at', 'desc')->paginate($perPage);

        // Return raw pagination data (Laravel default)
        return response()->json($jobs);
    }

  public function checkEligibility(Request $request)
{
    $user = auth('api')->user();
    $job  = Job::findOrFail($request->jobId);

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

    // -------------------------
    // 1) Extract text from CV
    // -------------------------
    $cvText = '';

    // Try Smalot parser first
    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($cvPath);
        $cvText = $pdf->getText() ?? '';
    } catch (\Throwable $e) {
        info("Smalot parser failed for: $cvPath — " . $e->getMessage());
        $cvText = '';
    }

    // If empty, try OCR fallback
    if (strlen(trim($cvText)) === 0) {
        try {
            info("CV parsing returned empty text. Trying OCR... File: $cvPath");
            $ocrText = (new TesseractOCR($cvPath))->run();
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

    // Basic cleaning
    $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
    $cvText = preg_replace('/\s+/', ' ', $cvText);
    $cvText = trim($cvText);

    // -------------------------
    // 2) Build prompt & call AI
    // -------------------------
    $prompt = <<<PROMPT
Compare the following CV and Job Description.
Return ONLY valid JSON with the following fields:
- matchPercentage (0-100)
- matchedSkills (list of matched skills/requirements)
- missingSkills (list of missing skills/requirements)
- recommendations (short advice for improvement)

--- JOB DESCRIPTION ---
{$job->description}

--- CV TEXT ---
{$cvText}
PROMPT;

    try {
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->chat()->create([
            'model'       => 'gpt-4o-mini', // adjust if you use another model
            'messages'    => [
                ['role' => 'system', 'content' => 'You are a CV-job matching assistant. Always respond with valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.2,
        ]);

        $aiOutput = $response->choices[0]->message->content ?? '';
        // Remove triple-backtick fences if present
        $cleanOutput = preg_replace('/^```(json)?/m', '', $aiOutput);
        $cleanOutput = preg_replace('/```$/m', '', $cleanOutput);
        $cleanOutput = trim($cleanOutput);

        $analysis = json_decode($cleanOutput, true);

        if (json_last_error() !== JSON_ERROR_NONE || ! is_array($analysis)) {
            // preserve raw AI output for debugging
            return response()->json([
                'success' => false,
                'message' => 'AI response could not be parsed',
                'raw'     => $aiOutput,
            ], 500);
        }

        // Safely decrement user's subscription checks (only after success)
        DB::table('subscriptions')->where('user_id', $user->id)->decrement('checks', 1);

        // Log usage (be tolerant of different usage field names)
        $tokensUsed = $response->usage->totalTokens ?? $response->usage->total_tokens ?? 0;
        DB::table('usage_activities')->insert([
            'user_id'     => $user->id,
            'action'      => 'check_eligibility',
            'status'      => 'success',
            'message'     => 'Check Eligibility',
            'tokens_used' => $tokensUsed,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // Return structured response
        return response()->json([
            'success'         => true,
            'matchPercentage' => $analysis['matchPercentage'] ?? null,
            'matchedSkills'   => $analysis['matchedSkills'] ?? [],
            'missingSkills'   => $analysis['missingSkills'] ?? [],
            'recommendations' => $analysis['recommendations'] ?? '',
            'cv_path'         => asset('storage/' . $user->cv_path),
            'cv_excerpt'      => substr($cvText, 0, 300) . '...',
        ]);
    } catch (\Throwable $aiError) {
        info("OpenAI call failed: " . $aiError->getMessage());
        // Optional: log failed usage attempt
        DB::table('usage_activities')->insert([
            'user_id'    => $user->id,
            'action'     => 'check_eligibility',
            'status'     => 'failed',
            'message'    => 'OpenAI call failed: ' . $aiError->getMessage(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        return response()->json([
            'success' => false,
            'message' => 'AI service error: ' . $aiError->getMessage(),
        ], 500);
    }
}
        public function revampCv(Request $request)
        {

            $user = auth('api')->user();
            $job  = Job::findOrFail($request->jobId);
            info($user);

            // ✅ Ensure CV exists
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

            // ✅ Extract text from CV
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($cvPath);
                $cvText = $pdf->getText() ?? '';

                if (strlen(trim($cvText)) === 0) {
                    info("CV parsing returned empty text. File: $cvPath");
                }

                // Basic cleaning
                $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
                $cvText = preg_replace('/\s+/', ' ', $cvText);
                $cvText = trim($cvText);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read CV: ' . $e->getMessage(),
                ], 500);
            }

            // ✅ Build AI prompt
            $prompt = <<<PROMPT
You are a professional CV writer and career branding specialist.

Task:
Rewrite and enhance the following CV text to make it highly tailored and optimized for the given job description and title.

Requirements:
- Keep the formatting structured and recruiter-friendly with clear HTML section headers:
  - Summary
  - Key Skills
  - Experience
  - Education
- Focus on aligning the candidate’s achievements, experience, and skills with the job description.
- Improve language, grammar, and phrasing to sound polished and professional.
- Use concise bullet points for skills and experience where relevant.
- Do not fabricate false information — only enhance or rephrase existing content.
- Format the output in valid **HTML** (use `<h2>`, `<ul>`, `<li>`, `<p>`, etc.).
- If any section data is missing, omit that section gracefully.
- Return **ONLY** valid JSON with this field:
  - "revampedCv" (HTML formatted CV content, well-structured)

--- PERSONAL DETAILS ---
{$user->name}, {$user->email}, {$user->phone}
--- JOB DESCRIPTION ---
{$job->description}
--- HIRING COMPANY ---
{$job->company}
--- JOB TITLE ---
{$job->title}
--- ORIGINAL CV TEXT ---
{$cvText}

Example output structure:
{
  "revampedCv": "<h2>Summary</h2><p>Results-driven professional with proven experience in...</p><h2>Key Skills</h2><ul><li>Team Leadership</li><li>Project Management</li></ul><h2>Experience</h2><p>...</p><h2>Education</h2><p>...</p>"
}
PROMPT;

            // ✅ Call OpenAI
            $client   = OpenAI::client(env('OPENAI_API_KEY'));
            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are an expert CV revamp assistant. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            $aiOutput = $response->choices[0]->message->content ?? '';

            // ✅ Cleanup (remove code fences if present)
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
            DB::table('subscriptions')->where('user_id', $user->id)->decrement('cv', 1);
            DB::table('usage_activities')->insert([
                'user_id'     => $user->id,
                'action'      => 'cv_revamp',
                'status'      => 'success',
                'message'     => 'CV Revamp Generation',
                'tokens_used' => $response->usage->totalTokens ?? 0,

                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
            // ✅ Return structured response
            return response()->json([
                'success'    => true,
                'revampedCv' => $analysis['revampedCv'] ?? '',
                // 'recommendations' => $analysis['recommendations'] ?? [],
                'cv_path'    => asset('storage/' . $user->cv_path),
            ]);
        }

        public function coverLetter(Request $request)
        {
            $user = auth('api')->user();

            $job = Job::findOrFail($request->jobId);

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

            // Extract text from CV
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf    = $parser->parseFile($cvPath);
                $cvText = $pdf->getText() ?? '';

                if (strlen(trim($cvText)) === 0) {
                    info("CV parsing returned empty text. File: $cvPath");
                }

                // Clean text
                $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
                $cvText = preg_replace('/\s+/', ' ', $cvText);
                $cvText = trim($cvText);
            } catch (\Exception $e) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to read CV: ' . $e->getMessage(),
                ], 500);
            }

            // Build AI prompt for cover letter
            $prompt = <<<PROMPT
You are a professional career coach and expert cover letter writer.

Task:
Generate a personalized, professional, and compelling cover letter tailored to the job description below, using the candidate’s CV text and personal details to highlight relevant experience, skills, and achievements.

Requirements:
- Address the letter to the hiring manager if possible (e.g., "Dear Hiring Manager," or use the company name if appropriate).
- Mention the candidate’s name and optionally include their contact information at the top (email and phone).
- Highlight the candidate’s most relevant qualifications and achievements that match the job description.
- Keep the tone confident, polite, and professional.
- Length: 150–250 words (concise but detailed enough to show fit).
- End with a strong closing paragraph or call to action (e.g., expressing interest in an interview).
- Format the output in valid **HTML** with paragraph tags and line breaks.
- If any field (like company name or phone) is missing, omit it gracefully.
- Do not fabricate false information — use the provided CV text and personal details.
- Return **ONLY** valid JSON with this field:
  - "coverLetter" (HTML formatted)

--- PERSONAL DETAILS ---
{$user->name}, {$user->email}, {$user->phone}
--- JOB DESCRIPTION ---
{$job->description}
--- HIRING COMPANY ---
{$job->company}
--- JOB TITLE ---
{$job->title}
--- CV TEXT ---
{$cvText}

Example output structure:
{
  "coverLetter": "<p><strong>{$user->name}</strong><br>{$user->email} | {$user->phone}</p><p>Dear Hiring Manager,</p><p>I am excited to apply for the [Job Title] position at [Company]. ...</p><p>Kind regards,<br>{$user->name}</p>"
}
PROMPT;

            // Call OpenAI
            $client   = OpenAI::client(env('OPENAI_API_KEY'));
            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are an expert cover letter assistant. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            $aiOutput = $response->choices[0]->message->content ?? '';

            // Cleanup JSON formatting
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
            DB::table('subscriptions')->where('user_id', $user->id)->decrement('coverletters', 1);
            DB::table('usage_activities')->insert([
                'user_id'     => $user->id,
                'action'      => 'email_template',
                'status'      => 'success',
                'message'     => 'Cover letter Generation',
                'tokens_used' => $response->usage->totalTokens ?? 0,

                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Return structured response
            return response()->json([
                'success'     => true,
                'coverLetter' => $analysis['coverLetter'] ?? '',
                // 'recommendations'=> $analysis['recommendations'] ?? [],
                'cv_path'     => asset('storage/' . $user->cv_path),
            ]);
        }

        public function emailTemplate(Request $request)
        {
            $user = auth('api')->user();

            $job = Job::findOrFail($request->jobId);

            // Build AI prompt for email template
            $prompt = <<<PROMPT
You are a professional career coach and recruiter assistant.

Generate a concise, professional, and engaging email template that a candidate can send when applying for the following job.

IMPORTANT:
- Use the job details and the candidate's personal details provided below.
- Include the **email subject** as the first line (e.g., "Subject: Application for [Job Title] at [Company]").
- Write the email in HTML format (with paragraphs, line breaks, etc.).
- Keep it short, friendly, and professional (120–200 words).
- If any personal detail is missing, omit it gracefully.
- Return ONLY valid JSON with this field:
  - emailTemplate (HTML formatted)

--- PERSONAL DETAILS ---
{$user->name},{$user->email},{$user->phone}
--- JOB DESCRIPTION ---
{$job->description}
--- HIRING COMPANY ---
{$job->company}
--- JOB TITLE ---
{$job->title}

Example output structure:
{
  "emailTemplate": "<p><strong>Subject:</strong> Application for [Job Title] at [Company]</p><p>Dear Hiring Manager, ...</p><p>Kind regards,<br>[Candidate Name]</p>"
}
PROMPT;

            // Call OpenAI
            $client   = OpenAI::client(env('OPENAI_API_KEY'));
            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are an expert email template assistant. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.3,
            ]);

            $aiOutput = $response->choices[0]->message->content ?? '';

            // Cleanup JSON formatting
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
            DB::table('subscriptions')->where('user_id', $user->id)->decrement('emails', 1);
            // Log usage on success only
            DB::table('usage_activities')->insert([
                'user_id'     => $user->id,
                'action'      => 'email_template',
                'status'      => 'success',
                'message'     => 'Email template Generation',
                'tokens_used' => $response->usage->totalTokens ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            // Return structured response
            return response()->json([
                'success'  => true,
                'template' => $analysis['emailTemplate'] ?? '',
                'job_id'   => $job->id,
            ]);
        }
    }
