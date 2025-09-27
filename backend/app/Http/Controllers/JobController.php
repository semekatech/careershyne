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
Return ONLY valid JSON (no markdown, no code fences). The response MUST be a single JSON object with these fields:

- matchPercentage: integer 0-100
- matchedSkills: array of objects { "skill": string, "relevance": "high"|"medium"|"low", "description": string }
- missingSkills: array of objects { "skill": string, "importance": "high"|"medium"|"low", "description": string }
- recommendations: array of short strings
- overallAssessment: string

Example:
{
  "matchPercentage": 72,
  "matchedSkills": [
    { "skill": "Project Management", "relevance": "high", "description": "Led multiple projects..." }
  ],
  "missingSkills": [
    { "skill": "Cloud Computing", "importance": "high", "description": "No mention of AWS/GCP/Azure" }
  ],
  "recommendations": ["Add cloud experience", "Get AWS cert"],
  "overallAssessment": "Short paragraph..."
}

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

        // $analysis = json_decode($cleanOutput, true); // already present

        if (json_last_error() !== JSON_ERROR_NONE || !$analysis) {
            return response()->json([
                'success' => false,
                'message' => 'AI response could not be parsed',
                'code'    => 'AI_PARSE_ERROR',
                'raw'     => $aiOutput,
            ], 500);
        }

        // Ensure matchedSkills and missingSkills are arrays of associative arrays
        $ensureList = function ($list, $type = 'matched') {
            if (empty($list)) return [];

            // If already an array of arrays, return as-is
            if (is_array($list) && isset($list[0]) && is_array($list[0])) {
                return $list;
            }

            // If it's a string with several JSON objects on separate lines, try to extract JSON objects
            if (is_string($list)) {
                // find {...} blocks
                preg_match_all('/\{.*?\}/s', $list, $matches);
                $out = [];
                foreach ($matches[0] as $m) {
                    $decoded = json_decode($m, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $out[] = $decoded;
                    } else {
                        // fallback: parse simple "key": "value" pairs
                        // crude parser to extract skill, relevance/importance, description
                        preg_match('/"skill"\s*:\s*"([^"]+)"/i', $m, $s);
                        preg_match('/"(relevance|importance)"\s*:\s*"([^"]+)"/i', $m, $r);
                        preg_match('/"description"\s*:\s*"([^"]+)"/i', $m, $d);
                        $out[] = [
                            'skill' => $s[1] ?? trim(strip_tags($m)),
                            ($r[1] ?? ($type === 'matched' ? 'relevance' : 'importance')) => $r[2] ?? null,
                            'description' => $d[1] ?? null,
                        ];
                    }
                }
                return $out;
            }

            // If it's an array of strings like ['{ "skill":... }', '{ "skill":... }']
            if (is_array($list)) {
                $out = [];
                foreach ($list as $item) {
                    if (is_array($item)) {
                        $out[] = $item;
                        continue;
                    }
                    $decoded = json_decode($item, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $out[] = $decoded;
                    } else {
                        $out[] = ['skill' => $item, 'relevance' => null, 'description' => null];
                    }
                }
                return $out;
            }

            return [];
        };

        $analysis['matchedSkills'] = $ensureList($analysis['matchedSkills'] ?? [], 'matched');
        $analysis['missingSkills'] = $ensureList($analysis['missingSkills'] ?? [], 'missing');

        return response()->json([
            'success'         => true,
            'analysis'        => $analysis,
            'cv_path'         => asset('storage/' . $user->cv_path),
            'cv_excerpt'      => substr($cvText, 0, 300) . '...',
        ]);
    }
}
