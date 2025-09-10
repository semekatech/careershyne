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

class WhatsapController extends Controller
{

    public function index(Request $request)
    {
        info('Data received: ' . collect($request->all()));
        // Set the response header
        header('Content-Type: application/xml');
        // Retrieve POST data
        $device = $request->input('device');
        $message = $request->input('message'); // The message received
        $from = $request->input('from'); // The sender's phone number
        $name = $request->input('name'); // The name of the sender
        $participant = $request->input('participant') ?: null;
        $responseMessage = '';
        $timestamp = now()->toDateTimeString();
        // if (!$participant) {
        //     $this->storeNumber($request->from, $name);
        // }
    }



    public function fetchPayment(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $orders = MpesaPayment::orderByDesc('id')->get();
        return response()->json(['orders' => $orders], 200);
    }
}
