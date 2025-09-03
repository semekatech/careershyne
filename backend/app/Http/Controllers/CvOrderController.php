<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use Illuminate\Http\Request;

class CvOrderController extends Controller
{
   public function store(Request $request)
{
    // Validate
    $validated = $request->validate([
        'fullname' => 'required|string|max:255',
        'email'    => 'required|email',
        'phone'    => 'required|string|max:20',
        'cv'       => 'required|file|mimes:pdf,doc,docx|max:2048',
    ]);

    // Store file
    $cvPath = $request->file('cv')->store('cv_uploads', 'public');

    // Save to DB
    $cvOrder = CvOrder::create([
        'fullname' => $validated['fullname'],
        'email'    => $validated['email'],
        'phone'    => $validated['phone'],
        'status'   => 'pending',
        'cv_path'  => $cvPath,
    ]);

    return response()->json([
        'message' => 'CV Order submitted successfully!',
        'id'      => $cvOrder->id,  // ✅ return the ID
        'data'    => $cvOrder,
    ], 201);
}

}
