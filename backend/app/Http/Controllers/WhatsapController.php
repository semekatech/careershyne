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
        $messageLower = strtolower(trim($message));

        // Check if user already has a session
        $session = DB::table('whatsapp_sessions')->where('phone', $phone)->first();

        // If user types "cv" and has no session, create one
        if ($messageLower === 'cv' && !$session) {
            DB::table('whatsapp_sessions')->insert([
                'name' => $name,
                'phone' => $phone,
                'step' => 'initial',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $reply  = "👋 Hello $name,\n";
            $reply .= "Unlock your career potential with our professional CV services. Please choose an option below:\n\n";
            $reply .= "1️⃣ CV Review – Get expert feedback on your CV\n";
            $reply .= "2️⃣ CV Customization – Tailor your CV for specific job roles\n";
            $reply .= "3️⃣ CV Writing – Have a professional CV crafted for you\n\n";
            $reply .= "👉 Reply with the number of your choice (e.g., *1*) to continue.";

            return $this->sendMessage($phone, $reply);
        }

        // If user already has a session and replies with 1,2,3 update step
        if ($session && in_array($messageLower, ['1', '2', '3'])) {
            DB::table('whatsapp_sessions')
                ->where('phone', $phone)
                ->update([
                    'step' => $messageLower,
                    'updated_at' => now()
                ]);
            // Craft response based on chosen step
            switch ($messageLower) {
                case '1':
                    $reply = "✅ You chose *CV Review*. Our experts will review your CV and share feedback.";
                    break;
                case '2':
                    $reply = "✅ You chose *CV Customization*. We’ll tailor your CV to match specific job roles.";
                    break;
                case '3':
                    $reply = "✅ You chose *CV Writing*. A professional CV will be crafted for you from scratch.";
                    break;
            }

            return $this->sendMessage($phone, $reply);
        }
        // Default fallback
        return null;
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
