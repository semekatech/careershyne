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

class AiController extends Controller
{

    public function uploadCV(Request $request)
    {
        // ✅ 1. Validate input
        $request->validate([
            'file' => 'required|mimes:pdf|max:5120', // only PDF, max 5MB
        ]);

        // ✅ 2. Store file
        $path = $request->file('file')->store('cvs', 'public');
        // This stores in storage/app/public/cvs and makes it accessible if "public" disk is linked
        info('uploaded');
        // ✅ 3. Return response
        return response()->json([
            'success' => true,
            'message' => 'CV uploaded successfully.',
            'file_path' => asset('storage/' . $path),
            'file_name' => basename($path),
        ]);
    }
}
