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

    try {
        $parser = new \Smalot\PdfParser\Parser();
        $pdf    = $parser->parseFile($cvPath);
        $cvText = $pdf->getText() ?? '';

        if (strlen(trim($cvText)) === 0) {
            info("CV parsing returned empty text. File: $cvPath");
        }

        $cvText = preg_replace('/[^A-Za-z0-9\s.,!?;:\-()]/u', ' ', $cvText);
        $cvText = preg_replace('/\s+/', ' ', $cvText);
        $cvText = trim($cvText);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to read CV: ' . $e->getMessage(),
        ], 500);
    }

    // ✅ Send CV and Job description to OpenAI
    $client = OpenAI::client(env('OPENAI_API_KEY'));

    $prompt = "
    Compare the following CV and Job Description.
    Return a JSON response with the following fields:
    - matchPercentage (0-100)
    - matchedSkills (list of skills/requirements from job that are present in CV)
    - missingSkills (list of skills/requirements from job missing in CV)
    - recommendations (short advice on how candidate can improve CV to fit job better)

    --- JOB DESCRIPTION ---
    {$job->description}

    --- CV TEXT ---
    {$cvText}
    ";

    $response = $client->chat()->create([
        'model' => 'gpt-4o-mini',
        'messages' => [
            ['role' => 'system', 'content' => 'You are a CV-job matching assistant. Always respond with valid JSON only.'],
            ['role' => 'user', 'content' => $prompt],
        ],
        'temperature' => 0.2,
    ]);

    $aiOutput = $response->choices[0]->message->content;

    // Try parsing JSON
    $analysis = json_decode($aiOutput, true);
    if (!$analysis) {
        return response()->json([
            'success' => false,
            'message' => 'AI response could not be parsed',
            'raw'     => $aiOutput
        ], 500);
    }
  info($analysis);
    return response()->json([
        'success'         => true,
        'matchPercentage' => $analysis['matchPercentage'] ?? null,
        'matchedSkills'   => $analysis['matchedSkills'] ?? [],
        'missingSkills'   => $analysis['missingSkills'] ?? [],
        'recommendations' => $analysis['recommendations'] ?? '',
        'cv_path'         => asset('storage/' . $user->cv_path),
        'cv_excerpt'      => substr($cvText, 0, 300) . '...'
    ]);
}
}
