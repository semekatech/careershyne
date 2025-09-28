<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;
use Nette\Utils\Random;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use App\Models\User;
use DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UserController extends Controller
{

    public function fetchAll(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->where('role', '1109')->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $users = User::orderByDesc('id')->get();
        return response()->json(['users' => $users], 200);
    }



    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'role' => 'required',
                'status' => 'required|in:active,inactive',
                'industry_id' => 'required|exists:industries,id',
                'education_level_id' => 'required|exists:education_levels,id',
                'county_id' => 'required|exists:counties,id',
                'cv' => 'required|file|mimes:pdf|max:5120',
                'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => $request->role,
                'status' => $request->status,
                'password' => Hash::make('Careershyne@2025'),
            ]);

            if ($request->hasFile('cv')) {
                $user->cv_path = $request->file('cv')->store('cvs', 'public');
            }
            if ($request->hasFile('cover_letter')) {
                $user->cover_letter_path = $request->file('cover_letter')->store('cover_letters', 'public');
            }

            $user->industry_id = $request->industry_id;
            $user->education_level_id = $request->education_level_id;
            $user->county_id = $request->county_id;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'user' => $user,
            ], 201);
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Send validation errors as an array
            info($e->errors());
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
    public function update(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:20',
                'role' => 'required',
                'status' => 'required|in:active,inactive',
                'industry_id' => 'required|exists:industries,id',
                'education_level_id' => 'required|exists:education_levels,id',
                'county_id' => 'required|exists:counties,id',
                'cv' => 'nullable|file|mimes:pdf|max:5120',
                'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->role = $request->role;
            $user->status = $request->status;
            $user->industry_id = $request->industry_id;
            $user->education_level_id = $request->education_level_id;
            $user->county_id = $request->county_id;

            // Handle CV upload
            if ($request->hasFile('cv')) {
                // Delete old CV if exists
                if ($user->cv_path && Storage::disk('public')->exists($user->cv_path)) {
                    Storage::disk('public')->delete($user->cv_path);
                }
                $user->cv_path = $request->file('cv')->store('cvs', 'public');
            }

            // Handle Cover Letter upload
            if ($request->hasFile('cover_letter')) {
                if ($user->cover_letter_path && Storage::disk('public')->exists($user->cover_letter_path)) {
                    Storage::disk('public')->delete($user->cover_letter_path);
                }
                $user->cover_letter_path = $request->file('cover_letter')->store('cover_letters', 'public');
            }

            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'user' => $user,
            ], 200);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong.',
            ], 500);
        }
    }
    public function impersonateLogin(Request $request, User $user)
    {
        $admin = $request->user();

        if (!$admin || $admin->role != 1109) { // admin role
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        // Generate new token for the impersonated user
        $token = Str::random(60);
        $user->api_token = hash('sha256', $token);
        $user->save();

        // Determine redirect route
        $redirectRoute = 'dashboard';
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
    public function userLimits(Request $request)
    {
        $user = auth('api')->user();
        $user_id = $user->id;

        $limit = DB::table('subscriptions')->where('user_id', $user_id)->first();

        return response()->json([
            'cv' => $limit->cv ?? 0,
            'coverLetters' => $limit->coverletters ?? 0,
            'emails' => $limit->emails ?? 0,
            'plan' => $limit->plan ?? 'Free',
        ]);
    }
}
