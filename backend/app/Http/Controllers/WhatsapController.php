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
        if (!$participant) {
            $this->prepareMessage($request->from, $message, $name);
        }
    }

    public function prepareMessage($phone, $message, $name)
    {
        if (strtolower($message) === 'cv') {
            $message = "👋 Hello $name,\n";
            $message .= "Unlock your career potential with our professional CV services. Please choose an option below:\n\n";
            $message .= "1️⃣ CV Review – Get expert feedback on your CV\n";
            $message .= "2️⃣ CV Customization – Tailor your CV for specific job roles\n";
            $message .= "3️⃣ CV Writing – Have a professional CV crafted for you\n\n";
            $message .= "👉 Reply with the number of your choice (e.g., *1*) to continue.";

            $this->sendMessage($phone, $message);
        }

    }

    public function sendMessage($phone, $message)
    {
        $apiUrl = 'https://ngumzo.com/v1/send-message';
        $apiKey = 'tbPCCeImssS8tXSkNUNtCmhmxaPziR';
        $data = [
            'sender' => "254758428993",
            'number' => $phone,
            'message' => $message
        ];
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $apiKey  // Include your API key here
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
