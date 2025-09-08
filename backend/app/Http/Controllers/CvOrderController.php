<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use App\Models\User;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class CvOrderController extends Controller
{
    public function store(Request $request)
    {
        // basic validation
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email',
            'type'     => 'required|string',
            'phone'    => 'required|string|max:20',
            'cv'       => 'nullable|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // store uploaded CV if provided
        $cvPath = null;
        if ($request->hasFile('cv')) {
            $cvPath = $request->file('cv')->store('cv_uploads', 'public');
        }

        // determine amount
        $amount = $validated['type'] === 'cv' ? 200 : 500;
        if ($validated['type'] === 'cv') {
            $amount = 200;
        } else if ($validated['type'] === 'cvscratch') {
            $amount = 300;
        }
        $orderID = \Nette\Utils\Random::generate(7, '0-9');

        // save order
        $cvOrder = CvOrder::create([
            'fullname'   => $validated['fullname'],
            'email'      => $validated['email'],
            'phone'      => $validated['phone'],
            'type'       => $validated['type'],
            'amount'     => $amount,
            'orderID'    => $orderID,
            'status'     => 'pending',
            'cv_path'    => $cvPath,

            // store extended fields as JSON
            'location'   => $request->input('location'),
            'career_goal' => $request->input('careerGoal'),
            'skills'     => $request->input('skills'),
            'linkedin'   => $request->input('linkedin'),
            'portfolio'  => $request->input('portfolio'),
            'cover_role'  => $request->input('coverRole'),
            'cover_why'   => $request->input('coverWhy'),
            'cover_strengths' => $request->input('coverStrengths'),
            'education'  => $request->input('education'),
            'experience' => $request->input('experience'),
            'certifications' => $request->input('certifications'),
        ]);
            //   info('education'.$request->input('education'));
        return response()->json([
            'message' => 'CV Order submitted successfully!',
            'id'      => $orderID,
            'data'    => $cvOrder,
        ], 201);
    }

    public function show($id)
    {
        $order = CvOrder::where('orderID', $id)->firstOrFail();
        return response()->json([
            'data' => [
                'amount'     => $order->amount,
                'orderID'    => $order->orderID,
                'status'     => $order->status,
                'created_at' => $order->created_at->format('Y-m-d H:i:s'),
            ]
        ], 200);
    }



 public function fetchAll(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
         if(!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $orders= CvOrder::orderByDesc('id')->get();
        return response()->json(['orders' => $orders], 200);
    }
}
