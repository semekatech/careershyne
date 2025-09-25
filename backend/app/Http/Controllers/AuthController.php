<?php

namespace App\Http\Controllers;

use App\Models\Bid;
use App\Models\Campaign;
use App\Models\Subscription;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DB;
use App\Mail\WelcomeUserMail;
use App\Models\CvOrder;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
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

        // Generate token
        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();

        // Determine redirect route
        $redirectRoute = 'dashboard'; // default

        if ($user->role == 1098) {
            if (!$user->county_id || !$user->industry_id || !$user->education_level_id) {
                $redirectRoute = 'profile-setup';
            }
        }
        // info($redirectRoute);
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
                'role' => $user->role,
            ],
            'redirect' => $redirectRoute,
        ]);
    }


    public function register(Request $request)
    {
        info($request->all());

        // Validation
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|confirmed|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            // Create user
            $user = User::create([
                'name' => $request->fullName,
                'email' => $request->email,
                'role' => '1098',
                'phone' => $request->phone,
                'password' => Hash::make($request->password),
            ]);

            // Create subscription
            DB::table('subscriptions')->insert([
                'user_id' => $user->id,
                'plan' => 'Free',
                'limit' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send welcome email
            try {
                Mail::to($user->email)->send(new WelcomeUserMail($user));
            } catch (\Exception $e) {
                info('Failed to send welcome email: ' . $e->getMessage());
                return response()->json([
                    'status' => 'warning',
                    'message' => 'User registered, but welcome email failed to send',
                    'user' => $user
                ], 201);
            }

            return response()->json([
                'status' => 'success',
                'message' => 'User registered successfully',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            info('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'error',
                'message' => 'Registration failed: ' . $e->getMessage()
            ], 500);
        }
    }
    public function profileSetup(Request $request)
    {
        // Authenticate user
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // Validate incoming request
        $validated = $request->validate([
            'industry_id' => 'required|integer|exists:industries,id',
            'education_level_id' => 'required|integer|exists:education_levels,id',
            'county_id' => 'required|integer|exists:counties,id',
            'cv' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // max 5MB
            'coverLetterFile' => 'nullable|file|mimes:pdf,doc,docx|max:5120', // max 5MB
        ]);

        // Update user profile fields
        $user->industry_id = $validated['industry_id'];
        $user->education_level_id = $validated['education_level_id'];
        $user->county_id = $validated['county_id'];

        // Handle CV upload
        if ($request->hasFile('cv')) {
            $cvFile = $request->file('cv');
            $cvPath = $cvFile->store('cvs', 'public');
            $user->cv_path = $cvPath;
        }

        // Handle Cover Letter upload
        if ($request->hasFile('coverLetterFile')) {
            $coverLetterFile = $request->file('coverLetterFile');
            $coverLetterPath = $coverLetterFile->store('cover_letters', 'public'); // storage/app/public/cover_letters
            $user->cover_letter_path = $coverLetterPath;
        }

        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'User registered successfully',
            'user' => $user
        ], 201);
    }

    public function industries()

    {
        $industries = DB::table('industries')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($industries);
    }

    public function educationLevels()
    {
        $educationLevels = DB::table('education_levels')
            ->select('id', 'name')
            ->orderBy('id')
            ->get();

        return response()->json($educationLevels);
    }

    public function counties()
    {
        $counties = DB::table('counties')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return response()->json($counties);
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


    public function getStats(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ✅ Base query depends on role
        $baseQuery = CvOrder::query();
        if ($user->role === 'radio') {
            $baseQuery->where('ref', 'rd');
        }

        // ✅ General stats
        $all = (clone $baseQuery)->count();
        $pending = (clone $baseQuery)->where('status', 'pending')->count();
        $approved = (clone $baseQuery)->where('status', 'paid')->count();
        $totalAmount = (clone $baseQuery)->sum('amount');
        $totalPendingAmount = (clone $baseQuery)->where('status', 'pending')->sum('amount');
        $totalApprovedAmount = (clone $baseQuery)->where('status', 'paid')->sum('amount');

        // ✅ Graph Data: paid orders per day
        $paidOrdersByDay = (clone $baseQuery)
            ->where('status', 'paid')
            ->selectRaw("DATE(created_at) as date, COUNT(*) as total_orders, SUM(amount) as total_amount")
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get();
        info($paidOrdersByDay);
        return response()->json([
            'pending' => $pending,
            'approved' => $approved,
            'all' => $all,
            'totalAmount' => $totalAmount,
            'totalPendingAmount' => $totalPendingAmount,
            'totalApprovedAmount' => $totalApprovedAmount,
            'graphData' => $paidOrdersByDay
        ]);
    }



    public function userDetails(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();

        return response()->json([
            'id' => $user->id,
            'name' => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'role' => $user->role,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();

        // Base validation rules
        $rules = [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|max:255',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            // Social media links
            'tiktok' => 'nullable|url|max:255',
            'facebook' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'bio' => 'nullable|string|max:255',
            'location' => 'nullable|string|max:255',
            'dob' => 'nullable|string|max:255',
        ];

        // If password is present, require current_password and validate password confirmation
        if ($request->filled('password')) {
            $rules['current_password'] = 'required|string';
            $rules['password'] = 'nullable|string|min:6|confirmed';
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        // If password is being changed, validate current password
        if ($request->filled('password') && !Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'message' => 'Current password is incorrect.',
            ], 403);
        }

        // Update basic info
        $user->name = $request->name;
        $user->email = $request->email;
        $user->phone = $request->phone;

        // Update password
        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        // Upload photo
        if ($request->hasFile('photo')) {
            if ($user->photo) {
                Storage::disk('public')->delete($user->photo);
            }
            $path = $request->file('photo')->store('profiles', 'public');
            $user->photo = $path;
        }

        // Update social media
        $user->tiktok = $request->tiktok;
        $user->facebook = $request->facebook;
        $user->twitter = $request->twitter;
        $user->instagram = $request->instagram;
        $user->youtube = $request->youtube;
        $user->linkedin = $request->linkedin;
        $user->niche = $request->niche;
        $user->location = $request->location;
        $user->bio = $request->bio;
        $user->dob = $request->dob;
        $user->save();

        return response()->json([
            'message' => 'Profile updated successfully.',
            'user' => $user,
        ]);
    }

    public function getNotifications(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notifications = [];

        if ($user->role === 'promoter') {
            $platforms = ['tiktok', 'facebook', 'twitter', 'instagram', 'youtube'];
            $missing = [];

            foreach ($platforms as $platform) {
                if (empty($user->$platform)) {
                    $missing[] = ucfirst($platform);
                }
            }
            if (!empty($missing)) {
                $formattedList = collect($missing)->map(function ($item) {
                    return "<strong style='color:#db2777'>" . e($item) . "</strong>";
                })->implode(', ');

                $notifications[] = [
                    'id' => count($notifications) + 1,
                    'message' => "Your profile is missing the following social links: {$formattedList}. Adding them can improve your visibility and chances of getting hired by brands.",
                    'created_at' => now()->toDateTimeString(),
                ];
            }
        }


        return response()->json(['notifications' => $notifications]);
    }

    public function impersonateLogin(Request $request, User $user)
{
    $admin = $request->user(); // token-based auth
    if (!$admin || $admin->role != 1109) { // assuming 1109 = admin
        return response()->json(['message' => 'Unauthorized'], 403);
    }
    $token = Str::random(60);
    $user->api_token = hash('sha256', $token);
    $user->save();

    // Determine redirect route (same logic as normal login)
    $redirectRoute = 'dashboard'; // default
    if ($user->role == 1098) { // normal user
        if (!$user->county_id || !$user->industry_id || !$user->education_level_id) {
            $redirectRoute = 'profile-setup';
        }
    }
    return response()->json([
        'access_token' => $token,
        'token_type' => 'Bearer',
        'user' => [
            'id' => $user->id,
            'name' => $user->name,
            'photo' => $user->photo,
            'role' => $user->role,
        ],
        'redirect' => $redirectRoute,
        'impersonator_id' => $admin->id,
    ]);
}
}
