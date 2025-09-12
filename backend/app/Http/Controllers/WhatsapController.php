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
        // Whitelist allowed numbers for testing (international format only)
        $allowedPhones = ['254705030613', '254703644281']; 

        // Completely ignore non-campaign users
        if (!in_array($phone, $allowedPhones)) {
            return null; 
        }

        // Normalize message: lowercase
        $messageLower = strtolower(trim($message));
    
        // Check if user already has a campaign session
        $session = DB::table('whatsapp_sessions')
            ->where('phone', $phone)
            ->where('campaign', 'rlv_sept2025')
            ->first();

        // If user types "cv" and has no session, create one
        if ($messageLower === 'cv' && !$session) {
            DB::table('whatsapp_sessions')->insert([
                'name' => $name,
                'phone' => $phone,
                'step' => 'initial',
                'campaign' => 'rlv_sept2025',
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $reply  = "👋 Hello $name, welcome to *Career Shyne*!\n\n";
            $reply .= "We help you unlock your career potential with professional CV and cover letter services.\n\n";
            $reply .= "Here are our packages (all include FREE CV review and job application support):\n\n";

            $reply .= "1️⃣ *CV Revamp + Cover Letter (KES 200)*\n";
            $reply .= "   ✔ 1 CV revamp (ATS-friendly, keyword optimized)\n";
            $reply .= "   ✔ 1 tailored cover letter\n";
            $reply .= "   ✔ Industry-specific adjustments\n\n";
        
            $reply .= "2️⃣ *CV from Scratch + Cover Letter (KES 300)*\n";
            $reply .= "   ✔ CV crafted from scratch\n";
            $reply .= "   ✔ Personalized cover letter\n";
            $reply .= "   ✔ ATS-optimized formatting\n";
            $reply .= "   ✔ Tailored to your career goals\n\n";

            $reply .= "👉 Reply with *1* or *2* to continue.";

            return $this->sendMessage($phone, $reply);
        }

        // If user already has a session and replies with 1 or 2, update step
        if ($session && in_array($messageLower, ['1', '2'])) {
            DB::table('whatsapp_sessions')
                ->where('phone', $phone)
                ->where('campaign', 'rlv_sept2025')
                ->update([
                    'step' => $messageLower,
                    'updated_at' => now()
                ]);
            
            // Craft response based on chosen step
            switch ($messageLower) {
                case '1':
                    $reply  = "✅ You chose *CV Revamp + Cover Letter (KES 200)*.\n";
                    $reply .= "You’ll get ATS-friendly CV writing, a tailored cover letter, and job application guidance.\n\n";
                    $reply .= "👉 Proceed here: https://careershyne.com/order-cv?ref=rd";
                    break;

                case '2':
                    $reply  = "✅ You chose *CV from Scratch + Cover Letter (KES 300)*.\n";
                    $reply .= "You’ll get a CV crafted from scratch (ATS-optimized), a personalized cover letter, and full job application support.\n\n";
                    $reply .= "👉 Proceed here: https://careershyne.com/custom-cv-order?ref=rd";
                    break;
            }

            return $this->sendMessage($phone, $reply);
        }

        // Default fallback: silently ignore other inputs
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
