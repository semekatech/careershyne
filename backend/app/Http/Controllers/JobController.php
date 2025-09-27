<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
use OpenAI;
use App\Services\AIReviewService;
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
            // ✅ Wrap file in Symfony UploadedFile for consistency
            $file = new \Illuminate\Http\UploadedFile(
                $cvPath,
                basename($cvPath),
                mime_content_type($cvPath),
                null,
                true // mark as test (skip move checks)
            );

            // ✅ Extract CV text
            $cvText = $this->extractTextFromFile($file, 'CV');
            info("CV text extracted: " . substr($cvText, 0, 2000000) . "...");
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to read CV: ' . $e->getMessage(),
            ], 500);
        }

        // For now just simulate a match %
        $matchPercentage = rand(30, 100);
        $feedback = "Excellent fit!";
        if ($matchPercentage < 50) {
            $feedback = "Your CV does not align strongly with the requirements. Consider updating your skills.";
        } elseif ($matchPercentage < 80) {
            $feedback = "You meet most requirements but could improve on some skills or experience.";
        }

        return response()->json([
            'success'         => true,
            'matchPercentage' => $matchPercentage,
            'feedback'        => $feedback,
            'cv_path'         => asset('storage/' . $user->cv_path),
            'cv_excerpt'      => substr($cvText, 0, 300) . '...' // show snippet
        ]);
    }

    private function extractTextFromFile($file, $type = 'File')
    {
        $jobText = '';
        try {
              info("$type: Processing PDF.");
                [$filePath, $jobText] = $this->aiReview->extractText($file);
            $text = $this->aiReview->cleanText($jobText);
        } catch (\Exception $e) {
            info("$type: Text extraction failed - " . $e->getMessage());
            throw new \Exception("$type text extraction failed: " . $e->getMessage());
        }

        return $text;
    }
}
