<?php
namespace App\Http\Controllers;

use App\Models\Job;
use App\Models\User;
use App\Services\AIReviewService;
use DB;
use Google\Client as GoogleClient;
use Google\Service\Gmail\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use OpenAI;

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
        'type'                    => 'required', // can be array or string
        'experience'              => 'required|integer|min:0',
        'education'               => 'required|string|max:255',
        'salary'                  => 'required|string|max:255',
        'deadline'                => 'required|date',
        'field'                   => 'required', // can be array or string
        'description'             => 'required|string',
        'applicationInstructions' => 'required|string',
        'applicationEmail'        => 'required|email|max:255',
        'country'                 => 'required|string|max:255',
        'county'                  => 'required', // can be array or string
        'office'                  => 'nullable|string|max:255',
    ]);

    $postedBy = auth('api')->id();

    // Ensure all multi-select fields are arrays
    $fieldsArray = is_array($validated['field']) ? $validated['field'] : explode(',', $validated['field']);
    $countiesArray = is_array($validated['county']) ? $validated['county'] : explode(',', $validated['county']);
    $typesArray = is_array($validated['type']) ? $validated['type'] : explode(',', $validated['type']);

    $jobsCreated = [];

    foreach ($fieldsArray as $field) {
        $jobData = $validated;
        $jobData['field'] = trim($field);
        $jobData['county'] = implode(',', array_map('trim', $countiesArray));
        $jobData['type'] = implode(',', array_map('trim', $typesArray));
        $jobData['posted_by'] = $postedBy;
        $jobData['slug'] = Str::slug($validated['title'] . '-' . $field . '-' . time());
        $job = Job::create($jobData);
        $jobsCreated[] = $job;
    }
    return response()->json([
        'success' => true,
        'message' => count($jobsCreated) > 1 ? 'Jobs created successfully' : 'Job created successfully',
        'data' => $jobsCreated,
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
    public function saveJobInterest($id)
    {

        info('saving job');
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Check if the user already marked this job as interested
        $exists = DB::table('job_interests')
            ->where('user_id', $user->id)
            ->where('job_id', $id)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Already marked as interested'], 403);
        }
        // Insert a new record
        DB::table('job_interests')->insert([
            'user_id'    => $user->id,
            'job_id'     => $id,
            'status'     => 'saved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        $adminMsg = "New Job Saved,Kindly login and check";
        $this->sendMessage('254705030613', $adminMsg); // Admin phone
        $this->sendMessage('254703644281', $adminMsg);
        return response()->json(['message' => 'Job interest saved successfully'], 200);
    }
    public function saveJobNotInterested($id)
    {

        info('saving job');
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Check if the user already marked this job as interested
        $exists = DB::table('jobs_not_interested')
            ->where('user_id', $user->id)
            ->where('job_id', $id)
            ->exists();
        if ($exists) {
            return response()->json(['message' => 'Already marked as interested'], 403);
        }
        // Insert a new record
        DB::table('jobs_not_interested')->insert([
            'user_id'    => $user->id,
            'job_id'     => $id,
            'status'     => 'saved',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        // $adminMsg = "New Job Saved,Kindly login and check";
        // $this->sendMessage('254705030613', $adminMsg); // Admin phone
        // $this->sendMessage('254703644281', $adminMsg);
        return response()->json(['message' => 'Job interest saved successfully'], 200);
    }
    public function fetchPersonalizedJobs(Request $request)
    {
        $userId = auth('api')->id();

        $query = DB::table('job_listings')
            ->leftJoin('industries', 'industries.id', '=', 'job_listings.field')
            ->leftJoin('job_interests', function ($join) use ($userId) {
                $join->on('job_interests.job_id', '=', 'job_listings.id')
                    ->where('job_interests.user_id', '=', $userId);
            })
            ->select(
                'job_listings.*',
                'industries.name as field_name',
                DB::raw("CASE WHEN job_interests.id IS NOT NULL THEN 'saved' ELSE 'not_saved' END AS save_status")
            )
            ->where('job_listings.field', $userId ? auth('api')->user()->industry_id : null)
        // ✅ Exclude jobs user marked as not interested
            ->whereNotIn('job_listings.id', function ($subquery) use ($userId) {
                $subquery->select('job_id')
                    ->from('jobs_not_interested')
                    ->where('user_id', $userId);
            });
        // 🔍 Optional search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_listings.title', 'like', "%{$search}%")
                    ->orWhere('job_listings.company', 'like', "%{$search}%")
                    ->orWhere('job_listings.county', 'like', "%{$search}%")
                    ->orWhere('job_listings.country', 'like', "%{$search}%")
                    ->orWhere('industries.name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $jobs    = $query->orderBy('job_listings.created_at', 'desc')->paginate($perPage);

        return response()->json($jobs);
    }

    public function fetchSavedJobs(Request $request)
    {
        $userId   = auth('api')->id();
        $userRole = auth('api')->user()->role;

        $query = DB::table('job_listings')
            ->join('job_interests', 'job_interests.job_id', '=', 'job_listings.id')
            ->leftJoin('users', 'users.id', '=', 'job_interests.user_id')
            ->leftJoin('industries', 'industries.id', '=', 'job_listings.field')
            ->leftJoin('job_applications', function ($join) {
                $join->on('job_applications.job_id', '=', 'job_listings.id')
                    ->on('job_applications.user_id', '=', 'job_interests.user_id');
            })
            ->select(
                'job_listings.*',
                'industries.name as field_name',
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'job_interests.created_at as saved_at',
                'job_interests.status as application_status',
                'users.cv_path as existing_cv',
                'users.cover_letter_path as existing_cover_letter',
                // Application details
                'job_applications.created_at as applied_on',
                'job_applications.subject',
                'job_applications.body',
                'job_applications.cv_path as application_cv',
                'job_applications.cover_letter_path as application_cover_letter',
                'job_applications.created_at as applied_on',
                DB::raw("'saved' AS save_status")
            );

        // Filter by current user if role is NOT admin (1109)
        if ($userRole != 1109) {
            $query->where('job_interests.user_id', $userId);
        }

        // Optional search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_listings.title', 'like', "%{$search}%")
                    ->orWhere('job_listings.company', 'like', "%{$search}%")
                    ->orWhere('job_listings.county', 'like', "%{$search}%")
                    ->orWhere('job_listings.country', 'like', "%{$search}%")
                    ->orWhere('industries.name', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $jobs    = $query->orderBy('job_interests.created_at', 'desc')->paginate($perPage);

        return response()->json($jobs);
    }

    public function fetchAppliedJobs(Request $request)
    {
        $userId   = auth('api')->id();
        $userRole = auth('api')->user()->role;

        $query = DB::table('job_listings')
            ->join('job_interests', 'job_interests.job_id', '=', 'job_listings.id')
            ->leftJoin('users', 'users.id', '=', 'job_interests.user_id')
            ->leftJoin('industries', 'industries.id', '=', 'job_listings.field')
            ->leftJoin('job_applications', function ($join) {
                $join->on('job_applications.job_id', '=', 'job_listings.id')
                    ->on('job_applications.user_id', '=', 'job_interests.user_id');
            })
            ->select(
                'job_listings.*',
                'industries.name as field_name',
                'users.id as user_id',
                'users.name as user_name',
                'users.email as user_email',
                'job_interests.created_at as saved_at',
                'job_interests.status as application_status',
                'users.cv_path as existing_cv',
                'users.cover_letter_path as existing_cover_letter',
                // Application details
                'job_applications.created_at as applied_on',
                'job_applications.subject',
                'job_applications.body',
                'job_applications.cv_path as application_cv',
                DB::raw("'saved' AS save_status")
            )
            ->where('job_interests.status', 'applied');

        // Filter by current user if not admin (role != 1109)
        if ($userRole != 1109) {
            $query->where('job_interests.user_id', $userId);
        }

        // Optional search filter
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('job_listings.title', 'like', "%{$search}%")
                    ->orWhere('job_listings.company', 'like', "%{$search}%")
                    ->orWhere('job_listings.county', 'like', "%{$search}%")
                    ->orWhere('job_listings.country', 'like', "%{$search}%")
                    ->orWhere('industries.name', 'like', "%{$search}%")
                    ->orWhere('users.name', 'like', "%{$search}%");
            });
        }

        $perPage = $request->get('per_page', 10);
        $jobs    = $query->orderBy('job_applications.created_at', 'desc')
            ->paginate($perPage);

        return response()->json($jobs);
    }

    public function generateContent(Request $request, $jobId)
    {
        try {
            // ✅ Get user ID from request instead of auth (or fallback to auth)
            $userId = $request->input('userId') ?? auth('api')->id();
            $user   = User::find($userId);
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

            // 2️⃣ Extract CV text
            $cvText = $this->extractTextFromFile(new \Illuminate\Http\File($cvPath), 'CV');
            $cvText = $this->trimText($cvText);

            // 3️⃣ Fetch job from DB
            $job = Job::find($jobId);
            if (! $job) {
                return response()->json([
                    'success' => false,
                    'message' => 'Job not found',
                ], 404);
            }

            $description = strip_tags($job->description);

            // 4️⃣ Build AI Prompt
            $prompt = <<<PROMPT
You are a professional career consultant and email writing expert.

Task:
Generate a professional **job application email** for the user strictly using the provided CV text, personal information, and job description. Do NOT invent any details or make assumptions. Only use what is explicitly provided.

### Personal Information:
Name: {$user->name}
Email: {$user->email}
Phone: {$user->phone}

### Job Description:
Title: {$job->title}
Company: {$job->company}
Description: {$description}

### CV Text:
$cvText

### Instructions:
- Use only the information from the CV and personal details above.
- Do NOT invent degrees, experience, achievements, or other personal details.
- Keep the email concise (max 300 words).
- Use a formal yet engaging tone suitable for job applications.
- Highlight only relevant skills and experience from the CV.
- Avoid restating the entire CV — focus on alignment with the job.
- Structure the email using <p> tags for paragraphs.
- End politely with a call-to-action (like “Looking forward to your response”).
- Return ONLY valid JSON in this format:

{
  "subject": "<Email subject here>",
  "body": "<HTML formatted email body here>"
}
PROMPT;

            // 5️⃣ Call OpenAI API
            $client   = OpenAI::client(env('OPENAI_API_KEY'));
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

            if (json_last_error() !== JSON_ERROR_NONE || ! isset($decoded['subject'], $decoded['body'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to parse AI response.',
                    'raw'     => $aiOutput,
                ], 500);
            }
            // 8️⃣ Return final response
            return response()->json([
                'success' => true,
                'subject' => $decoded['subject'],
                'body'    => $decoded['body'],
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function fetchPublicJobs(Request $request)
    {
        $query = Job::query()
            ->leftJoin('industries', 'industries.id', '=', 'job_listings.field')
            ->select('job_listings.*', 'industries.name as field_name');
        // Optional search
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
        if ($request->filled('county')) {
            $query->where('job_listings.county', 'like', "%{$request->county}%");
        }
        if ($request->filled('type')) {
            $query->where('job_listings.type', 'like', "%{$request->type}%");
        }
        if ($request->filled('category')) {
            info($request->category);
            $query->where('job_listings.field', $request->category);
        }
        $perPage = $request->get('per_page', 9);
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
        // 1️⃣ Extract text from CV
        // -------------------------
        $cvText = '';

        // 🔹 Try Smalot parser first
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf    = $parser->parseFile($cvPath);
            $cvText = $pdf->getText() ?? '';
        } catch (\Throwable $e) {
            info("Smalot parser failed for: $cvPath — " . $e->getMessage());
            $cvText = '';
        }

        // 🔹 If empty, try OCR (Imagick + Tesseract)
        if (strlen(trim($cvText)) === 0) {
            try {
                info("CV parsing returned empty text. Trying OCR... File: $cvPath");

                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImage($cvPath); // Convert PDF pages to images
                $imagick->setImageFormat('png');

                $ocrText = '';

                foreach ($imagick as $index => $page) {
                    $tmpImg = tempnam(sys_get_temp_dir(), 'ocr_') . '.png';
                    $page->writeImage($tmpImg);

                    // Run tesseract on the image
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

        // 🔹 Final fallback check
        if (strlen(trim($cvText)) === 0) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read CV. Please upload a valid, text-based or scanned PDF file.',
            ], 422);
        }

        // 🔹 Basic cleaning
        $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
        $cvText = preg_replace('/\s+/', ' ', $cvText);
        $cvText = trim($cvText);

        // -------------------------
        // 2️⃣ Build AI prompt
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

        // -------------------------
        // 3️⃣ Call OpenAI
        // -------------------------
        try {
            $client   = OpenAI::client(env('OPENAI_API_KEY'));
            $response = $client->chat()->create([
                'model'       => 'gpt-4o-mini',
                'messages'    => [
                    ['role' => 'system', 'content' => 'You are a CV-job matching assistant. Always respond with valid JSON only.'],
                    ['role' => 'user', 'content' => $prompt],
                ],
                'temperature' => 0.2,
            ]);

            $aiOutput = $response->choices[0]->message->content ?? '';

            // Cleanup JSON fences
            $cleanOutput = preg_replace('/^```(json)?/m', '', $aiOutput);
            $cleanOutput = preg_replace('/```$/m', '', $cleanOutput);
            $cleanOutput = trim($cleanOutput);

            $analysis = json_decode($cleanOutput, true);

            if (json_last_error() !== JSON_ERROR_NONE || ! is_array($analysis)) {
                return response()->json([
                    'success' => false,
                    'message' => 'AI response could not be parsed',
                    'raw'     => $aiOutput,
                ], 500);
            }

            // -------------------------
            // 4️⃣ Record usage
            // -------------------------
            DB::table('subscriptions')->where('user_id', $user->id)->decrement('checks', 1);
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

            // -------------------------
            // 5️⃣ Return results
            // -------------------------
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

        $cvText = '';

        // -------------------------
        // 1️⃣ Try Smalot PDF Parser
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
        // 2️⃣ Fallback to OCR if empty
        // -------------------------
        if (strlen(trim($cvText)) === 0) {
            try {
                info("CV parsing returned empty text. Trying OCR... File: $cvPath");

                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImage($cvPath); // Convert PDF pages to images
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
        // 3️⃣ Basic cleaning
        // -------------------------
        $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
        $cvText = preg_replace('/\s+/', ' ', $cvText);
        $cvText = trim($cvText);

        // -------------------------
        // 4️⃣ Build AI prompt
        // -------------------------
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
- Format the output in valid **HTML** (use <h2>, <ul>, <li>, <p>, etc.).
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
PROMPT;

        // -------------------------
        // 5️⃣ Call OpenAI
        // -------------------------
        try {
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
            return response()->json([
                'success'    => true,
                'revampedCv' => $analysis['revampedCv'] ?? '',
                'cv_path'    => asset('storage/' . $user->cv_path),
            ]);
        } catch (\Throwable $aiError) {
            info("OpenAI call failed: " . $aiError->getMessage());
            DB::table('usage_activities')->insert([
                'user_id'    => $user->id,
                'action'     => 'cv_revamp',
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
    public function coverLetter(Request $request)
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

        $cvText = '';

        // -------------------------
        // 1️⃣ Try Smalot PDF Parser
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
        // 2️⃣ Fallback to OCR if empty
        // -------------------------
        if (strlen(trim($cvText)) === 0) {
            try {
                info("CV parsing returned empty text. Trying OCR... File: $cvPath");

                $imagick = new \Imagick();
                $imagick->setResolution(300, 300);
                $imagick->readImage($cvPath); // Convert PDF pages to images
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
        // 3️⃣ Clean CV text
        // -------------------------
        $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
        $cvText = preg_replace('/\s+/', ' ', $cvText);
        $cvText = trim($cvText);

        // -------------------------
        // 4️⃣ Build AI prompt
        // -------------------------
        $prompt = <<<PROMPT
You are a professional career coach and expert cover letter writer.

Task:
Generate a personalized, professional, and compelling cover letter tailored to the job description below, using the candidate’s CV text and personal details to highlight relevant experience, skills, and achievements.

Requirements:
- Address the letter to the hiring manager if possible (e.g., "Dear Hiring Manager," or use the company name if appropriate).
- Mention the candidate’s name and optionally include their contact information at the top (email and phone).
- Highlight the candidate’s most relevant qualifications and achievements that match the job description.
- Keep the tone confident, polite, and professional.
- Length: 150–250 words.
- Format the output in valid HTML with paragraph tags and line breaks.
- Do not fabricate false information.
- Return ONLY valid JSON with this field:
  - "coverLetter"

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
PROMPT;

        // -------------------------
        // 5️⃣ Call OpenAI
        // -------------------------
        try {
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
                'action'      => 'cover_letter',
                'status'      => 'success',
                'message'     => 'Cover letter Generation',
                'tokens_used' => $response->usage->totalTokens ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            return response()->json([
                'success'     => true,
                'coverLetter' => $analysis['coverLetter'] ?? '',
                'cv_path'     => asset('storage/' . $user->cv_path),
            ]);
        } catch (\Throwable $aiError) {
            info("OpenAI call failed: " . $aiError->getMessage());

            DB::table('usage_activities')->insert([
                'user_id'    => $user->id,
                'action'     => 'cover_letter',
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

    public function emailTemplate(Request $request)
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

        $cvText = '';

        // -------------------------
        // 1️⃣ Try Smalot PDF Parser
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
        // 2️⃣ Fallback to OCR if empty
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
        // 3️⃣ Clean CV text
        // -------------------------
        $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
        $cvText = preg_replace('/\s+/', ' ', $cvText);
        $cvText = trim($cvText);

        // -------------------------
        // 4️⃣ Build AI prompt
        // -------------------------
        $prompt = <<<PROMPT
You are a professional career coach and recruiter assistant.

Task:
Generate a concise, professional, and engaging email template that a candidate can send when applying for the following job. Use the candidate’s CV text to highlight relevant skills, achievements, and experience.

Requirements:
- Include the **email subject** as the first line (e.g., "Subject: Application for [Job Title] at [Company]").
- Write the email in HTML format (paragraphs, line breaks, etc.).
- Keep it short, friendly, and professional (120–200 words).
- Do not fabricate false information.
- Return ONLY valid JSON with this field:
  - "email_template"

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
PROMPT;

        // -------------------------
        // 5️⃣ Call OpenAI
        // -------------------------
        try {
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
            DB::table('usage_activities')->insert([
                'user_id'     => $user->id,
                'action'      => 'email_template',
                'status'      => 'success',
                'message'     => 'Email template Generation',
                'tokens_used' => $response->usage->totalTokens ?? 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            return response()->json([
                'success'        => true,
                'email_template' => $analysis['email_template'] ?? '',
                'cv_path'        => asset('storage/' . $user->cv_path),
                'job_id'         => $job->id,
            ]);
        } catch (\Throwable $aiError) {
            info("OpenAI call failed: " . $aiError->getMessage());

            DB::table('usage_activities')->insert([
                'user_id'    => $user->id,
                'action'     => 'email_template',
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

    public function categories()
    {
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $industries = DB::table('industries as i')
            ->leftJoin('job_listings as j', 'i.id', '=', 'j.field')
            ->leftJoin('users as u', 'i.id', '=', 'u.industry_id')
            ->select(
                'i.id',
                'i.name',
                DB::raw('COUNT(DISTINCT j.id) as jobs_count'),
                DB::raw('COUNT(DISTINCT u.id) as subscribers_count')
            )
            ->groupBy('i.id', 'i.name')
            ->orderBy(DB::raw('COUNT(DISTINCT j.id)'), 'desc')
            ->get();

        return response()->json($industries);
    }
    public function storeCategory(Request $request)
    {
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $request->validate([
            'name' => 'required|string|max:255|unique:industries,name',
        ]);

        $id = DB::table('industries')->insertGetId([
            'name' => $request->name,
            // 'created_at' => now(),
            // 'updated_at' => now(),
        ]);

        return response()->json([
            'message'  => 'Category added successfully',
            'category' => DB::table('industries')->where('id', $id)->first(),
        ]);
    }

    public function deleteCategory($id)
    {
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $exists = DB::table('industries')->where('id', $id)->exists();

        if (! $exists) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        DB::table('industries')->where('id', $id)->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
    public function updateCategory(Request $request, $id)
    {
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $exists = DB::table('industries')->where('id', $id)->exists();

        if (! $exists) {
            return response()->json(['message' => 'Category not found'], 404);
        }

        DB::table('industries')->where('id', $id)->update([
            'name' => $request->name,
            // 'updated_at' => now(),
        ]);

        return response()->json([
            'message'  => 'Category updated successfully',
            'category' => DB::table('industries')->where('id', $id)->first(),
        ]);
    }
    public function applyOnBehalf(Request $request, $jobId)
    {
        info($request->all());
        $request->validate([
            'user_id'          => 'required|integer',
            'subject'          => 'required|string|max:255',
            'body'             => 'required|string',
            'cv'               => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cv_url'           => 'nullable',
            'cover_letter'     => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter_url' => 'nullable',
        ]);

        $userId  = $request->input('user_id');
        $subject = $request->input('subject');
        $body    = $request->input('body');

        $user         = \App\Models\User::findOrFail($userId);
        $userFullName = str_replace(' ', '_', $user->name ?? 'Applicant');
        if (! $user->gmail_access_token) {
            return response()->json(['message' => 'User has not authorized Gmail.'], 403);
        }

        try {
            // ===== Gmail Client Setup =====
            $client = new GoogleClient();
            $client->setClientId(env('GMAIL_CLIENT_ID'));
            $client->setClientSecret(env('GMAIL_CLIENT_SECRET'));
            $client->setRedirectUri(env('GMAIL_REDIRECT_URI'));
            $client->addScope('https://www.googleapis.com/auth/gmail.send');

            $token = json_decode($user->gmail_access_token, true);
            $client->setAccessToken($token);

            // Refresh token if expired
            if ($client->isAccessTokenExpired()) {
                if (! empty($user->gmail_refresh_token)) {
                    $newToken = $client->fetchAccessTokenWithRefreshToken($user->gmail_refresh_token);
                    $client->setAccessToken($newToken);

                    $user->update([
                        'gmail_access_token'     => json_encode($client->getAccessToken()),
                        'gmail_refresh_token'    => $newToken['refresh_token'] ?? $user->gmail_refresh_token,
                        'gmail_token_expires_at' => now()->addSeconds($newToken['expires_in'] ?? 3600),
                    ]);
                } else {
                    return response()->json(['message' => 'Token expired. Please reconnect Gmail.'], 401);
                }
            }

            $gmail = new \Google\Service\Gmail($client);

            // ===== Email Composition =====
            $boundary         = uniqid(rand(), true);
            $rawMessageString = "From: {$user->email}\r\n";
            $rawMessageString .= "To: info@careershyne.com\r\n";
            $rawMessageString .= "Subject: {$subject}\r\n";
            $rawMessageString .= "MIME-Version: 1.0\r\n";
            $rawMessageString .= "Content-Type: multipart/mixed; boundary=\"{$boundary}\"\r\n\r\n";
            $rawMessageString .= "--{$boundary}\r\n";
            $rawMessageString .= "Content-Type: text/html; charset=utf-8\r\n"; // ✅ HTML body
            $rawMessageString .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
            $rawMessageString .= $body . "\r\n";

            // ===== Attach CV =====
            $cvPath = null;
            if ($request->hasFile('cv')) {
                $file      = $request->file('cv');
                $cvExt     = $file->getClientOriginalExtension();
                $cvName    = "{$userFullName}_CV.{$cvExt}";
                $cvContent = chunk_split(base64_encode(file_get_contents($file->getRealPath())));
                $cvPath    = $file->storeAs('applications/cv', $cvName, 'public');
            } elseif ($request->filled('cv_url')) {
                $cvUrl = $request->input('cv_url');

                if (! str_starts_with($cvUrl, 'http')) {
                    $cvUrl = 'https://careershyne.com/storage/' . ltrim($cvUrl, '/');
                }

                try {
                    $cvRaw = file_get_contents($cvUrl);
                } catch (\Exception $e) {
                    info('CV download failed', ['url' => $cvUrl, 'error' => $e->getMessage()]);
                    return response()->json(['message' => 'Could not fetch CV from provided URL.'], 422);
                }

                $cvExt     = pathinfo(parse_url($cvUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'pdf';
                $cvName    = "{$userFullName}_CV.{$cvExt}";
                $cvContent = chunk_split(base64_encode($cvRaw));
                $cvPath    = 'applications/cv/' . $cvName;
                Storage::disk('public')->put($cvPath, $cvRaw);
            } else {
                return response()->json(['message' => 'CV is required (file or URL).'], 422);
            }

            $rawMessageString .= "--{$boundary}\r\n";
            $rawMessageString .= "Content-Type: application/octet-stream; name=\"{$cvName}\"\r\n";
            $rawMessageString .= "Content-Transfer-Encoding: base64\r\n";
            $rawMessageString .= "Content-Disposition: attachment; filename=\"{$cvName}\"\r\n\r\n";
            $rawMessageString .= $cvContent . "\r\n";

            // ===== Attach Cover Letter (optional) =====
            $coverLetterPath = null;
            if ($request->hasFile('cover_letter')) {
                $file            = $request->file('cover_letter');
                $clExt           = $file->getClientOriginalExtension();
                $clName          = "{$userFullName}_Cover_Letter.{$clExt}";
                $clContent       = chunk_split(base64_encode(file_get_contents($file->getRealPath())));
                $coverLetterPath = $file->storeAs('applications/cover_letters', $clName, 'public');
            } elseif ($request->filled('cover_letter_url')) {
                $clUrl = $request->input('cover_letter_url');

                if (! str_starts_with($clUrl, 'http')) {
                    $clUrl = 'https://careershyne.com/storage/' . ltrim($clUrl, '/');
                }

                try {
                    $clRaw = file_get_contents($clUrl);
                } catch (\Exception $e) {
                    info('Cover Letter download failed', ['url' => $clUrl, 'error' => $e->getMessage()]);
                    $clRaw = null;
                }

                if ($clRaw) {
                    $clExt           = pathinfo(parse_url($clUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'pdf';
                    $clName          = "{$userFullName}_Cover_Letter.{$clExt}";
                    $clContent       = chunk_split(base64_encode($clRaw));
                    $coverLetterPath = 'applications/cover_letters/' . $clName;
                    Storage::disk('public')->put($coverLetterPath, $clRaw);
                }
            }

            if (! empty($clContent ?? null)) {
                $rawMessageString .= "--{$boundary}\r\n";
                $rawMessageString .= "Content-Type: application/octet-stream; name=\"{$clName}\"\r\n";
                $rawMessageString .= "Content-Transfer-Encoding: base64\r\n";
                $rawMessageString .= "Content-Disposition: attachment; filename=\"{$clName}\"\r\n\r\n";
                $rawMessageString .= $clContent . "\r\n";
            }

            $rawMessageString .= "--{$boundary}--";

            // Encode for Gmail API
            $rawMessage = base64_encode($rawMessageString);
            $rawMessage = str_replace(['+', '/', '='], ['-', '_', ''], $rawMessage);

            // Send Gmail
            $message = new \Google\Service\Gmail\Message();
            $message->setRaw($rawMessage);
            $gmail->users_messages->send('me', $message);

            // ===== Save to Database =====

            try {
                $admin = auth('api')->user();
                // Insert the job application
                DB::table('job_applications')->insert([
                    'job_id'            => $jobId,
                    'user_id'           => $userId,
                    'applied_by'        => $admin->id,
                    'subject'           => $subject,
                    'body'              => $body,
                    'cv_path'           => $cvPath,
                    'cover_letter_path' => $coverLetterPath,
                    'created_at'        => now(),
                    'updated_at'        => now(),
                ]);

                // Update job interest status
                DB::table('job_interests')
                    ->where('job_id', $jobId)
                    ->where('user_id', $userId)
                    ->update([
                        'status'     => 'applied',
                        'updated_at' => now(),
                    ]);

            } catch (\Exception $e) {
                // Log the error for debugging
                Log::error('Job application failed', [
                    'job_id'  => $jobId,
                    'user_id' => $userId,
                    'error'   => $e->getMessage(),
                ]);

            }

            return response()->json([
                'message' => 'Application sent and saved successfully!',
            ]);
        } catch (\Exception $e) {
            info('Apply Error', ['error' => $e->getMessage()]);
            return response()->json([
                'message' => 'Error sending application',
                'error'   => $e->getMessage(),
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

    public function sendMessage($phone, $message)
    {
        $apiUrl = 'https://ngumzo.com/v1/send-message';
        $apiKey = 'tbPCCeImssS8tXSkNUNtCmhmxaPziR';
        $data   = [
            'sender'  => "254758428993",
            'number'  => $phone,
            'message' => $message,
        ];
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $apiKey, // Include your API key here
        ]);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        // Execute the cURL request and get the response
        $response = curl_exec($ch);
        // Check for errors
        if ($response === false) {
            // Log the error (you can handle this more gracefully in production)
            error_log('cURL Error: ' . curl_error($ch));
        }
        // Close the cURL session
        curl_close($ch);
        error_log('Response: ' . $response);
    }

}
