<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
use OpenAI;
use App\Services\AIReviewService;
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
            'company' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'experience' => 'required|integer|min:0',
            'education' => 'required|string|max:255',
            'salary' => 'required|string|max:255',
            'deadline' => 'required|date',
            'field' => 'required|string|max:255',
            'description' => 'required|string',
            'applicationInstructions' => 'required|string',
            'country' => 'required|string|max:255',
            'county' => 'required|string|max:255',
            'office' => 'required|string|max:255',
        ]);
        $validated['posted_by'] = auth('api')->id();
        $validated['slug'] = Str::slug($validated['title'] . '-' . time());
        $job = Job::create($validated);
        return response()->json([
            'success' => true,
            'message' => 'Job created successfully',
            'data' => $job
        ], 201);
    }
    public function fetchAll(Request $request)
    {
        // Optional: implement search
        $query = Job::query();

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('company', 'like', "%{$search}%")
                    ->orWhere('county', 'like', "%{$search}%")
                    ->orWhere('country', 'like', "%{$search}%");
            });
        }
        $perPage = $request->get('per_page', 10);
        $jobs = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json($jobs);
    }

    public function checkEligibility(Request $request)
    {
        $user = auth('api')->user();
        $job  = Job::findOrFail($request->jobId);
        $limit = DB::table('subscriptions')->where('user_id', $user->id)->first();
        if ($limit->checks == 0) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your limit Eligibility checks. Please upgrade your subscription plan.'
            ], 403);
        }
        // ✅ Ensure CV exists
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

        // ✅ Call OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'system', 'content' => 'You are a CV-job matching assistant. Always respond with valid JSON only.'],
                ['role' => 'user', 'content' => $prompt],
            ],
            'temperature' => 0.2,
        ]);

        $aiOutput = $response->choices[0]->message->content ?? '';

        // ✅ Cleanup (remove ```json fences if present)
        $cleanOutput = preg_replace('/^```(json)?/m', '', $aiOutput);
        $cleanOutput = preg_replace('/```$/m', '', $cleanOutput);
        $cleanOutput = trim($cleanOutput);

        $analysis = json_decode($cleanOutput, true);

        if (json_last_error() !== JSON_ERROR_NONE || !$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'AI response could not be parsed',
                'raw'     => $aiOutput,
            ], 500);
        }
        DB::table('subscriptions')->where('user_id', $user->id)->decrement('checks', 1);
        // ✅ Return structured response
        return response()->json([
            'success'         => true,
            'matchPercentage' => $analysis['matchPercentage'] ?? null,
            'matchedSkills'   => $analysis['matchedSkills'] ?? [],
            'missingSkills'   => $analysis['missingSkills'] ?? [],
            'recommendations' => $analysis['recommendations'] ?? '',
            'cv_path'         => asset('storage/' . $user->cv_path),
            'cv_excerpt'      => substr($cvText, 0, 300) . '...',
        ]);
    }
    public function revampCv(Request $request)
    {


        $user = auth('api')->user();
        $limit = DB::table('subscriptions')->where('user_id', $user->id)->first();
        if ($limit->cv == 0) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your limit for CV revamping. Please upgrade your subscription plan.'
            ], 403);
        }
        $job  = Job::findOrFail($request->jobId);
        info($user);

        // ✅ Ensure CV exists
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
You are a professional CV writer. Rewrite and enhance the following CV text so that it is tailored for the given job description.
Keep formatting structured into clear sections:
- Summary
- Key Skills
- Experience
- Education

Return ONLY valid JSON with the following fields:
- revampedCv (HTML formatted CV content, well-structured)

--- JOB DESCRIPTION ---
{$job->description}

--- ORIGINAL CV TEXT ---
{$cvText}
PROMPT;

        // ✅ Call OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
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

        if (json_last_error() !== JSON_ERROR_NONE || !$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'AI response could not be parsed',
                'raw'     => $aiOutput,
            ], 500);
        }
        DB::table('subscriptions')->where('user_id', $user->id)->decrement('cv', 1);
        // ✅ Return structured response
        return response()->json([
            'success'       => true,
            'revampedCv'    => $analysis['revampedCv'] ?? '',
            // 'recommendations' => $analysis['recommendations'] ?? [],
            'cv_path'       => asset('storage/' . $user->cv_path),
        ]);
    }

    public function coverLetter(Request $request)
    {
        $user = auth('api')->user();
        $limit = DB::table('subscriptions')->where('user_id', $user->id)->first();
        if ($limit->coverletters == 0) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your limit for CV revamping. Please upgrade your subscription plan.'
            ], 403);
        }
        $job  = Job::findOrFail($request->jobId);

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
You are a professional career coach and cover letter writer. Using the following CV text, generate a personalized cover letter tailored for the job description.
- Keep it concise, professional, and engaging.
- Highlight relevant experience, skills, and achievements from the CV.
- Address the letter to the hiring manager if possible.
- End with a strong call to action.
- Return ONLY valid JSON with fields:
  - coverLetter (HTML formatted)

--- JOB DESCRIPTION ---
{$job->description}

--- CV TEXT ---
{$cvText}
PROMPT;

        // Call OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
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

        if (json_last_error() !== JSON_ERROR_NONE || !$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'AI response could not be parsed',
                'raw'     => $aiOutput,
            ], 500);
        }
        DB::table('subscriptions')->where('user_id', $user->id)->decrement('coverletters', 1);
        // Return structured response
        return response()->json([
            'success'       => true,
            'coverLetter'   => $analysis['coverLetter'] ?? '',
            // 'recommendations'=> $analysis['recommendations'] ?? [],
            'cv_path'       => asset('storage/' . $user->cv_path),
        ]);
    }

    public function emailTemplate(Request $request)
    {
        $user = auth('api')->user();
        $limit = DB::table('subscriptions')->where('user_id', $user->id)->first();
        if ($limit->emails == 0) {
            return response()->json([
                'success' => false,
                'message' => 'You have reached your limit for Email templates. Please upgrade your subscription plan.'
            ], 403);
        }
        $job = Job::findOrFail($request->jobId);

        // Build AI prompt for email template
        $prompt = <<<PROMPT
You are a professional career coach and recruiter assistant.
Generate a concise, professional, and engaging email template that a candidate can send when applying for the following job:

--- JOB DESCRIPTION ---
{$job->description}

Return ONLY valid JSON with fields:
  - emailTemplate (HTML formatted)
PROMPT;

        // Call OpenAI
        $client = OpenAI::client(env('OPENAI_API_KEY'));
        $response = $client->chat()->create([
            'model' => 'gpt-4o-mini',
            'messages' => [
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

        if (json_last_error() !== JSON_ERROR_NONE || !$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'AI response could not be parsed',
                'raw'     => $aiOutput,
            ], 500);
        }
        DB::table('subscriptions')->where('user_id', $user->id)->decrement('emails', 1);
        // Return structured response
        return response()->json([
            'success'       => true,
            'template' => $analysis['emailTemplate'] ?? '',
            'job_id'        => $job->id,
        ]);
    }
}
