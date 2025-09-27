<?php

namespace App\Http\Controllers;

use App\Models\Job;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use DB;
class JobController extends Controller
{
    /**
     * Store a new job
     */
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
    $request->validate([
        'jobId' => 'required|integer|exists:jobs,id',
    ]);

    $job = Job::findOrFail($request->jobId);

    // Generate a random match between 30% and 100%
    $matchPercentage = rand(30, 100);

    // Pick feedback based on the random percentage
    $feedback = "Excellent fit!";
    if ($matchPercentage < 50) {
        $feedback = "Your CV does not align strongly with the requirements. Consider updating your skills.";
    } elseif ($matchPercentage < 80) {
        $feedback = "You meet most requirements but could improve on some skills or experience.";
    }

    return response()->json([
        'matchPercentage' => $matchPercentage,
        'feedback'        => $feedback,
    ]);
}


}
