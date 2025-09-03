<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Models\Campaign;
use App\Models\Subscription;
use Illuminate\Support\Facades\Storage;





class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
        $campaigns = Campaign::where('user_id', $user->id)->get();
        return response()->json(['campaigns' => $campaigns], 200);
    }

    public function toggleStatus($id)
    {
        $campaign = Campaign::findOrFail($id);
        $campaign->status = $campaign->status == 1 ? 0 : 1;
        $campaign->save();
        return response()->json([
            'message' => 'Campaign status updated successfully.',
            'status'  => $campaign->status
        ]);
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|min:10|max:60',
            'brand' => 'required|string|max:255',
            'website' => 'nullable|url|max:255',
            'about' => 'required|string',
            'startDate' => 'required|date',
            'endDate' => 'required|date',
            'targetAge' => 'required|string',
            'targetInterests' => 'required|string',
            'primaryGoal' => 'required|string',
            'platforms' => 'required|array',
            'contentFormats' => 'nullable|string',
            'budget' => 'required|string',
            'brief' => 'required|string',
            'guidance' => 'required|string',
            'eligibility' => 'required|string',
            'assets' => 'required|array|min:1',
            'assets.*' => 'required|image|mimes:jpg,jpeg,png',
        ]);

        if ($validator->fails()) {
            info($validator->errors());
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Store logo
        $logoPath = null;
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('campaigns/logos', 'public');
        }

        // Store assets
        $assetPaths = [];
        if ($request->hasFile('assets')) {
            foreach ($request->file('assets') as $asset) {
                $assetPaths[] = $asset->store('campaigns/assets', 'public');
            }
        }
        $subscription = Subscription::where('user_id', $request->user_id)->first();

        if ($subscription && $subscription->limit > 0) {
            $subscription->limit = $subscription->limit - 1;
            $subscription->save();
        }
        // Save to DB
        $campaign = Campaign::create([
            'title' => $request->title,
            'brand' => $request->brand,
            'website' => $request->website,
            'about' => $request->about,
            'status' => 3,
            'start_date' => $request->startDate,
            'residence' => $request->residence,
            'end_date' => $request->endDate,
            'application_deadline' => $request->applicationDeadline,
            'locations' => $request->residence,
            'target_age' => $request->targetAge,
            'target_interests' => $request->targetInterests,
            'primary_goal' => $request->primaryGoal,
            'kpis' => 'none',
            'user_id' => $request->user_id,
            'platforms' => $request->platforms,
            'content_formats' => $request->contentFormats,
            'hashtags' => 'none',
            'compensation_type' => 'none',
            'budget' => $request->budget,
            'brief' => $request->brief,
            'guidance' => $request->guidance,
            'eligibility' => $request->eligibility,
            'logo' => '',
            'assets' => $assetPaths,
        ]);

        return response()->json([
            'message' => 'Campaign created successfully!',
            'data' => $campaign
        ]);
    }
    public function update(Request $request)
    {
        try {
            info($request->all());

            $validator = Validator::make($request->all(), [
                'title' => 'required|string|min:10|max:60',
                'brand' => 'required|string|max:255',
                'website' => 'nullable|max:255',
                'about' => 'required|string',
                'start_date' => 'required|date',
                'end_date' => 'required|date',
                'target_age' => 'required|string',
                'target_interests' => 'required|string',
                'primary_goal' => 'required|string',
                'platforms' => 'required|array',
                'content_formats' => 'nullable|string',
                'budget' => 'required|string',
                'brief' => 'required|string',
                'guidance' => 'required|string',
                'eligibility' => 'required|string',
                // 'assets' => 'required|array|min:1',
                'assets.*' => 'required|image|mimes:jpg,jpeg,png',
            ]);

            if ($validator->fails()) {
                info('Validation failed during campaign update', [
                    'errors' => $validator->errors()->toArray()
                ]);
                $friendlyErrors = [];
                foreach ($validator->errors()->messages() as $field => $messages) {
                    $friendlyErrors[$field] = $messages[0];
                }
                return response()->json([
                    'message' => 'Some fields failed validation. Please check your input.',
                    'errors' => $friendlyErrors
                ], 422);
            }
            $campaign = Campaign::findOrFail($request->id);


            $finalAssets = [];
            if ($request->has('existing_assets')) {
                $existing = $request->input('existing_assets');
                $existing = is_array($existing) ? $existing : [$existing];
                foreach ($existing as &$asset) {
                    $asset = str_replace(url('storage') . '/', '', $asset);
                }
                $finalAssets = $existing;
            }
            // Then, handle new uploaded files
            if ($request->hasFile('assets')) {
                foreach ($request->file('assets') as $asset) {
                    $path = $asset->store('campaigns/assets', 'public');
                    info('Uploaded new asset path: ' . $path);
                    $finalAssets[] = $path;
                }
            }
            // Save final asset array
            $campaign->assets = $finalAssets;
            // Update fields
            $campaign->title = $request->title;
            $campaign->brand = $request->brand;
            $campaign->website = $request->website;
            $campaign->about = $request->about;
            $campaign->start_date = $request->start_date;
            $campaign->residence = $request->residence;
            $campaign->end_date = $request->end_date;
            $campaign->locations = $request->residence;
            $campaign->target_age = $request->target_age;
            $campaign->target_interests = $request->target_interests;
            $campaign->primary_goal = $request->primary_goal;
            $campaign->platforms = $request->platforms;
            $campaign->content_formats = $request->content_formats;
            $campaign->budget = $request->budget;
            $campaign->brief = $request->brief;
            $campaign->guidance = $request->guidance;
            $campaign->eligibility = $request->eligibility;
            $campaign->save();
            return response()->json([
                'message' => 'Campaign updated successfully!',
                'data' => $campaign
            ]);
        } catch (\Exception $e) {
            info('Error updating campaign: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'An unexpected error occurred while updating the campaign.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }
        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();
        info($token);
        // Return plain token to client (store securely on client)
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
            ],
        ]);
    }
    public function verifyToken(Request $request)
    {
        $authHeader = $request->header('Authorization');

        if (!$authHeader || !str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['valid' => false, 'message' => 'Authorization header missing or invalid'], 401);
        }

        $plainToken = substr($authHeader, 7);
        $hashedToken = hash('sha256', $plainToken);

        $user = User::where('api_token', $hashedToken)->first();

        if (!$user) {
            return response()->json(['valid' => false, 'message' => 'Invalid token'], 401);
        }

        return response()->json([
            'valid' => true,
            'user' => $user,
        ]);
    }
    /**
     * Store a newly created resource in storage.
     */

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $campaign = Campaign::findOrFail($id);
        return response()->json($campaign);
    }

    /**
     * Update the specified resource in storage.
     */


    /**
     * Remove the specified resource from storage.
     */ public function fetchAll()
    {
        return Campaign::where('status', 1)->orderByDesc('id')->get();
    }
}
