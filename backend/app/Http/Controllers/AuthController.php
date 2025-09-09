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
        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'photo' => $user->photo,
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

    public function getStats(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
        $campaigns = Campaign::where('user_id', $user->id)->pluck('id')->toArray();
        return response()->json([
            'campaigns' => Campaign::where('user_id', $user->id)->count(),
            'pending' => Campaign::where('user_id', $user->id)->where('status', 3)->count(),
            'approved' => Campaign::where('user_id', $user->id)->whereIn('status', [0, 1])->count(),
            'team' => Bid::whereIn('campaign_id', $campaigns)->count(),
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
}
