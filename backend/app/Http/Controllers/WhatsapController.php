<?php
namespace App\Http\Controllers;

use App\Models\MpesaPayment;
use App\Models\User;
use DB;
use Illuminate\Http\Request;

class WhatsapController extends Controller
{

    public function index(Request $request)
    {
        header('Content-Type: application/xml');
        info('here');
        $message     = $request->input('message');
        $phone       = $request->input('from');
        $name        = $request->input('name');
        $participant = $request->input('participant') ?: null;
        if (! $participant) {
            $this->prepareMessage($phone, $message, $name);
        }
    }

    public function prepareMessage($phone, $message, $name)
    {
        info('we are here');

        $phone          = preg_replace('/\D/', '', $phone); // normalize phone number
        $messageTrimmed = trim($message);

        // Check if phone already exists
        $existingLead = DB::table('leads')->where('phone', $phone)->first();
        // Only proceed if it's a new lead and message matches exactly (case-insensitive)
        if (! $existingLead && strcasecmp($messageTrimmed, 'Hello! Can I get more info on this?') === 0) {
            // Save new lead
            DB::table('leads')->insert([
                'name'       => $name,
                'phone'      => $phone,
                'message'    => $message,
                'campaign'   => 'ss',
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Send default welcome message
            $reply = "👋 Hello $name!\n\n"
                . "Thanks for reaching out to *CareerShyne! 🎯*\n"
                . "We’re here to help you land your next job faster and smarter.\n\n"
                . "Here’s what you get when you join the *CareerShyne Job Program*:\n"
                . "✅ Daily job updates in your preferred field.\n"
                . "✅ Up to 30 top jobs applied on your behalf every month.\n"
                . "✅ Tailored CVs, Cover Letters & Email templates customized for each role.\n"
                . "✅ Interview preparation & coaching so you stand out and get hired.\n\n"
                . "All this for just *KSh 1,000 for 30 days.*\n\n"
                . "Would you like to proceed with your enrollment today?";

            return $this->sendMessage($phone, $reply);
        }

        // Ignore everything else
        return null;
    }

    /**
     * Helper: Get package list
     */
    private function getPackageList($name)
    {
        $reply = "Here are our packages (all include FREE CV review and job application support):\n\n";

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

        return $reply;
    }

    public function sendMessage($phone, $message)
    {

        $apiUrl = 'https://ngumzo.com/v1/send-message';
        $apiKey = 'tbPCCeImssS8tXSkNUNtCmhmxaPziR';
        $data   = [
            'sender'  => "254758428993",
            'number'  => $phone,
            'message' => $message,
        ];
        $ch = curl_init($apiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'api-key: ' . $apiKey, // Include your API key here
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
        $user  = User::where('api_token', hash('sha256', $token))->first();
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $orders = MpesaPayment::orderByDesc('id')->get();
        return response()->json(['orders' => $orders], 200);
    }
}
