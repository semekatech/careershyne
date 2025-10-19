<?php
namespace App\Http\Controllers;

use App\Mail\WelcomeUserMail;
use App\Models\CvOrder;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);
        $ipKey    = 'login_attempts:ip:' . $request->ip();
        $emailKey = 'login_attempts:email:' . strtolower($request->email);

        // check if locked out by IP
        if (Cache::get($ipKey, 0) >= 10) {
            return response()->json([
                'message' => 'Too many failed login attempts from this IP. Please try again later.',
            ], 429);
        }

        // check if locked out by email
        if (Cache::get($emailKey, 0) >= 3) {
            return response()->json([
                'message' => 'Too many failed login attempts for this account. Please try again later.',
            ], 429);
        }

        $user = User::where('email', $request->email)->first();
        if (! $user || ! Hash::check($request->password, $user->password)) {
            // increase attempts
            Cache::add($ipKey, 0, now()->addMinutes(5));
            Cache::increment($ipKey);

            Cache::add($emailKey, 0, now()->addMinutes(5));
            Cache::increment($emailKey);

            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // ✅ success → reset counters
        Cache::forget($ipKey);
        Cache::forget($emailKey);

        // Generate token
        $token               = Str::random(60);
        $user->api_token     = hash('sha256', $token);
        $user->last_login_at = now();
        $user->save();
        // Determine redirect route
        $redirectRoute = 'dashboard';
        if ($user->role == 1098) {
            if (! $user->county_id || ! $user->industry_id || ! $user->education_level_id) {
                $redirectRoute = 'profile-setup';
            }
        }
        return response()->json([
            'access_token' => $token,
            'token_type'   => 'Bearer',
            'user'         => [
                'id'   => $user->id,
                'name' => $user->name,
                'role' => $user->role,
                'user_type' => $user->user_type,
            ],
            'redirect'     => $redirectRoute,
        ]);
    }

    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'fullName' => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20|unique:users,phone',
            'password' => 'required|string|confirmed|min:6',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Validation failed',
                'errors'  => $validator->errors(),
            ], 422);
        }
        try {
            // Create user
            $user = User::create([
                'name'     => $request->fullName,
                'email'    => $request->email,
                'role'     => '1098',
                'status'   => 'active',
                'phone'    => $request->phone,
                'password' => Hash::make($request->password),
            ]);
            // Create subscription
            DB::table('subscriptions')->insert([
                'user_id'      => $user->id,
                'plan'         => 'Free',
                'cv'           => 1,
                'coverletters' => 1,
                'emails'       => 1,
                'checks'       => 1,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
            // Send welcome email
            try {
                $adminMsg = "📢 New Jobseeker has Joined Careershyne: name: " . $request->fullName;
                $this->sendMessage('254705030613', $adminMsg);
                $this->sendMessage('254703644281', $adminMsg);
                Mail::to($user->email)->send(new WelcomeUserMail($user));
            } catch (\Exception $e) {
                info('Failed to send welcome email: ' . $e->getMessage());
                return response()->json([
                    'status'  => 'warning',
                    'message' => 'User registered, but welcome email failed to send',
                    'user'    => $user,
                ], 201);
            }
            return response()->json([
                'status'  => 'success',
                'message' => 'User registered successfully',
                'user'    => $user,
            ], 201);
        } catch (\Exception $e) {
            info('User registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'status'  => 'error',
                'message' => 'Registration failed: ' . $e->getMessage(),
            ], 500);
        }
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
    public function profileSetup(Request $request)
    {
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // Validate incoming request
        $validated = $request->validate([
            'name'               => 'nullable|string|max:255',
            'phone'              => 'nullable|string|max:20',
            'industry_id'        => 'nullable|integer|exists:industries,id',
            'education_level_id' => 'nullable|integer|exists:education_levels,id',
            'county_id'          => 'nullable|integer|exists:counties,id',
            'cv'                 => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            'cover_letter'       => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        // Update text fields
        if (isset($validated['name'])) {
            $user->name = $validated['name'];
        }
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        if (isset($validated['industry_id'])) {
            $user->industry_id = $validated['industry_id'];
        }
        if (isset($validated['education_level_id'])) {
            $user->education_level_id = $validated['education_level_id'];
        }
        if (isset($validated['county_id'])) {
            $user->county_id = $validated['county_id'];
        }

        // Handle CV upload
        if ($request->hasFile('cv')) {
            $cvPath        = $request->file('cv')->store('cvs', 'public');
            $user->cv_path = $cvPath;
        }

        // Handle Cover Letter upload
        if ($request->hasFile('cover_letter')) {
            $coverLetterPath         = $request->file('cover_letter')->store('cover_letters', 'public');
            $user->cover_letter_path = $coverLetterPath;
        }

        $user->save();

        return response()->json([
            'status'  => 'success',
            'message' => 'Profile updated successfully',
        ], 200);
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

        if (! $authHeader || ! str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['valid' => false, 'message' => 'Authorization header missing or invalid'], 401);
        }

        $plainToken  = substr($authHeader, 7);
        $hashedToken = hash('sha256', $plainToken);

        $user = User::where('api_token', $hashedToken)->first();
        if (! $user) {
            return response()->json(['valid' => false, 'message' => 'Invalid token'], 401);
        }
        return response()->json([
            'valid' => true,
            'user'  => $user,
        ]);
    }

    public function getStats(Request $request)
    {
        $token = $request->bearerToken();
        $user  = User::where('api_token', hash('sha256', $token))->first();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // ✅ Base query depends on role
        $baseQuery = CvOrder::query();
        if ($user->role === 'radio') {
            $baseQuery->where('ref', 'rd');
        }

        // ✅ General stats
        $all                 = (clone $baseQuery)->count();
        $pending             = (clone $baseQuery)->where('status', 'pending')->count();
        $approved            = (clone $baseQuery)->where('status', 'paid')->count();
        $totalAmount         = (clone $baseQuery)->sum('amount');
        $totalPendingAmount  = (clone $baseQuery)->where('status', 'pending')->sum('amount');
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
            'pending'             => $pending,
            'approved'            => $approved,
            'all'                 => $all,
            'totalAmount'         => $totalAmount,
            'totalPendingAmount'  => $totalPendingAmount,
            'totalApprovedAmount' => $totalApprovedAmount,
            'graphData'           => $paidOrdersByDay,
        ]);
    }

    public function userDetails(Request $request)
    {
        info('reached here');
        $token = $request->bearerToken();
        info($token);
        $user = User::where('api_token', hash('sha256', $token))->first();
        info($user);
        if (! $user) {
            info('user not found');
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        return response()->json([
            'id'    => $user->id,
            'name'  => $user->name,
            'phone' => $user->phone,
            'email' => $user->email,
            'role'  => $user->role,
            'user_type' => $user->user_type,
        ]);
    }

    public function fetchProfile(Request $request)
    {

        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'id'                 => $user->id,
            'name'               => $user->name,
            'email'              => $user->email,
            'phone'              => $user->phone,
            'industry_id'        => $user->industry_id,
            'education_level_id' => $user->education_level_id,
            'county_id'          => $user->county_id,
            'cv'                 => $user->cv_path,
            'cover_letter'       => $user->cover_letter_path,
            'photo_url'          => $user->photo_url,
        ]);
    }

    public function impersonateLogin(Request $request, User $user)
    {
        $admin = $request->user();

        if (! $admin || $admin->role != 1109) { // admin role
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate new token for the impersonated user
        $token           = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();

        // Determine redirect route
        $redirectRoute = 'dashboard';
        if ($user->role == 1098) { // normal user
            if (! $user->county_id || ! $user->industry_id || ! $user->education_level_id) {
                $redirectRoute = 'profile-setup';
            }
        }

        return response()->json([
            'access_token'    => $token,
            'token_type'      => 'Bearer',
            'user'            => [
                'id'    => $user->id,
                'name'  => $user->name,
                'photo' => $user->photo,
                'role'  => $user->role,
                'user_type' => $user->user_type,
            ],
            'redirect'        => $redirectRoute,
            'impersonator_id' => $admin->id,
        ]);
    }

    public function updatePassword(Request $request)
    {

        // info('hello');
        $user = auth('api')->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        // info('user ni' . $user);
        // Validate input
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password'     => 'required|string|min:8|confirmed',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'errors' => $validator->errors(),
            ], 422);
        }
        // info('sasaa' . $request->current_password);
        // Check if current password matches
        if (! Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Current password is incorrect.',
            ], 400);
        }

        // Update password
        $user->password = Hash::make($request->new_password);
        $user->save();
        return response()->json([
            'status'  => 'success',
            'message' => 'Password updated successfully.',
        ]);
    }
}
