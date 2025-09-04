<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use Illuminate\Http\Request;
use Nette\Utils\Random;

class PaymentController extends Controller
{
    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'phone'   => 'required|string|regex:/^254\d{9}$/',
            'amount'  => 'required|numeric|min:1',
            'orderID' => 'required|string|exists:cv_orders,orderID',
        ]);

        // Call STK Push service here (Daraja API / etc.)
        // For now just log
        info("Initiating STK Push", $validated);

        return response()->json([
            'success' => true,
            'message' => 'STK Push initiated'
        ]);
    }
}
