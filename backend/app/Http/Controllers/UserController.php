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
                'fullname' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'phone' => 'required|string|max:20',
                'role' => 'required|in:admin,user,radio',
                'status' => 'required|in:active,inactive',
                'industry_id' => 'nullable|exists:industries,id',
                'education_level_id' => 'nullable|exists:education_levels,id',
                'county_id' => 'nullable|exists:counties,id',
                'cv' => 'nullable|file|mimes:pdf|max:5120',
                'cover_letter' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
            ]);

            $user = User::create([
                'name' => $request->fullname,
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
}
