<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class CvOrderController extends Controller
{
    public function store(Request $request)
    {
        // Validate
        $validated = $request->validate([
            'fullname' => 'required|string|max:255',
            'email'    => 'required|email',
            'type'    => 'required|string',
            'phone'    => 'required|string|max:20',
            'cv'       => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        // Store file
        $cvPath = $request->file('cv')->store('cv_uploads', 'public');
        if ($validated['type'] == 'cv') {
            $amount = 200;
        } else {
            $amount = 500;
        }
        $orderID = Random::generate(7, '0-9');
        // Save to DB
        $cvOrder = CvOrder::create([
            'fullname' => $validated['fullname'],
            'email'    => $validated['email'],
            'phone'    => $validated['phone'],
            'type'    => $validated['type'],
            'amount' => $amount,
            'orderID' => $orderID,
            'status'   => 'pending',
            'cv_path'  => $cvPath,
        ]);

        return response()->json([
            'message' => 'CV Order submitted successfully!',
            'id'      => $orderID,
            'data'    => $cvOrder,
        ], 201);
    }
    public function show($id)
    {
        $order = CvOrder::where('orderID', $id)->first();
       info($order);
        return response()->json([
            'data' => $order
        ], 200);
    }
}
