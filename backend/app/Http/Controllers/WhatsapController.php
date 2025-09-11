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
        $allowedPhones = [
            '254705030613',   // George
            '254703644281'    // Nancy
        ];

        if (!in_array($phone, $allowedPhones)) {
            return $this->sendMessage($phone, "⚠️ Hi $name, this bot is currently in *testing mode* and not available for public use.");
        }


        $messageLower = strtolower(trim($message));
        $messageLower = preg_replace('/[^a-z0-9]/', '', $messageLower);

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

            $reply  = "👋 Hello $name, welcome to *Career Shyne*!\n\n";
            $reply .= "We help you unlock your career potential with professional CV and cover letter services. 🚀\n\n";
            $reply .= "Here are our packages (all include FREE CV review):\n\n";
            $reply .= "1️⃣ *CV Revamp + Cover Letter (KES 200)*\n";
            $reply .= "   ✔ 1 CV revamp (ATS-friendly, keyword optimized)\n";
            $reply .= "   ✔ 1 tailored cover letter\n";
            $reply .= "   ✔ Industry-specific adjustments\n\n";
            $reply .= "2️⃣ *CV from Scratch + Cover Letter (KES 300)*\n";
            $reply .= "   ✔ CV crafted from scratch\n";
            $reply .= "   ✔ Personalized cover letter\n";
            $reply .= "   ✔ ATS-optimized formatting\n";
            $reply .= "   ✔ Tailored to your career goals\n\n";
            $reply .= "3️⃣ *Career Success Package (KES 500)*\n";
            $reply .= "   ✔ 2 CV revamps (different roles/industries)\n";
            $reply .= "   ✔ 2 customized cover letters\n";
            $reply .= "   ✔ LinkedIn profile optimization\n";
            $reply .= "   ✔ Recruiter visibility boost\n\n";
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
                        $reply  = "✅ You chose *CV Revamp + Cover Letter (KES 200)*. Our experts will review your CV and share feedback.\n\n";
                        $reply .= "👉 Proceed here: https://careershyne.com/order-cv\n\n";
                        // $reply .= "--------------------------------------\n";
                        // $reply .= "🔁 You can also choose another service:\n";
                        // $reply .= "1️⃣ CV Revamp + Cover Letter (KES 200)\n";
                        // $reply .= "2️⃣ CV from Scratch + Cover Letter (KES 300)\n";
                        // $reply .= "3️⃣ Career Success Package (KES 500)\n\n";
                        // $reply .= "Reply with the number of your choice (e.g., *2*).";
                        break;

                    case '2':
                        $reply  = "✅ You chose *CV from Scratch + Cover Letter (KES 300)*. We’ll tailor your CV to match specific job roles.\n\n";
                        $reply .= "👉 Proceed here: https://careershyne.com/custom-cv-order\n\n";
                        // $reply .= "--------------------------------------\n";
                        // $reply .= "🔁 You can also choose another service:\n";
                        // $reply .= "1️⃣ CV Revamp + Cover Letter (KES 200)\n";
                        // $reply .= "2️⃣ CV from Scratch + Cover Letter (KES 300)\n";
                        // $reply .= "3️⃣ Career Success Package (KES 500)\n\n";
                        // $reply .= "Reply with the number of your choice (e.g., *3*).";
                        break;



                    case '3':
                        $reply  = "✅ You chose *Career Success Package (KES 500)*. Unlock your career potential with this premium option.\n\n";
                        $reply .= "👉 Proceed here: https://wa.me/254758428993?text=I%20want%20to%20unlock%20my%20career%20package%20with%20CareersHyne.\n\n";
                        // $reply .= "--------------------------------------\n";
                        // $reply .= "🔁 You can also choose another service:\n";
                        // $reply .= "1️⃣ CV Revamp + Cover Letter (KES 200)\n";
                        // $reply .= "2️⃣ CV from Scratch + Cover Letter (KES 300)\n";
                        // $reply .= "3️⃣ Career Success Package (KES 500)\n\n";
                        // $reply .= "Reply with the number of your choice (e.g., *1*).";
                        break;
                }

            return $this->sendMessage($phone, $reply);
        }

            // Handle invalid input
        if ($session && !in_array($messageLower, ['1','2','3'])) {
            $reply =  "❌ Invalid option.\n\n";
            $reply .= "Please reply with *1*, *2*, or *3* to continue.";
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
