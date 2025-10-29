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
        header('Content-Type: application/xml');

        $message = $request->input('message');
        $phone   = $request->input('from');
        $name    = $request->input('name');
        $participant = $request->input('participant') ?: null;

        if (!$participant) {
            $this->prepareMessage($phone, $message, $name);
        }
    }

    public function prepareMessage($phone, $message, $name)
    {
        // Whitelist allowed numbers for testing
        // $allowedPhones = ['254705030613', '254703644281'];
        // if (!in_array($phone, $allowedPhones)) {
        //     return null;
        // }

       $messageLower = strtolower(preg_replace('/\s+/', '', trim($message)));
        // Check for existing session
        $session = DB::table('whatsapp_sessions')
            ->where('phone', $phone)
            ->where('campaign', 'rlv_sept2025')
            ->first();
        if ($messageLower === 'cv') {
            if (!$session) {
                // Create new session
                DB::table('whatsapp_sessions')->insert([
                    'name'       => $name,
                    'phone'      => $phone,
                    'step'       => 'initial',
                    'campaign'   => 'rlv_sept2025',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Reply with package list
            return $this->sendMessage($phone, $this->getPackageList($name));
        }else{
             DB::table('leads')->insert([
                    'name'       => $name,
                    'phone'      => $phone,
                    'message'       => 'initial',
                    'campaign'   => 'ss',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
        }

        /**
         * CASE 2: User sends "1" or "2"
         */
        if (in_array($messageLower, ['1', '2'])) {
            if (!$session) {
                // New user sent 1 or 2 → show initial page first
                DB::table('whatsapp_sessions')->insert([
                    'name'       => $name,
                    'phone'      => $phone,
                    'step'       => 'initial',
                    'campaign'   => 'rlv_sept2025',
                    'created_at' => now(),
                    'updated_at' => now()
                ]);

                $reply = "👋 Hello $name!\n\n"
                    . "It looks like you want a CV service, but let's start with our packages first:\n\n"
                    . $this->getPackageList($name);

                return $this->sendMessage($phone, $reply);
            }

            // Existing session → update step
            DB::table('whatsapp_sessions')
                ->where('phone', $phone)
                ->where('campaign', 'rlv_sept2025')
                ->update([
                    'step'       => $messageLower,
                    'updated_at' => now()
                ]);

            // Craft response based on choice
            $reply = match ($messageLower) {
                '1' => "✅ You chose *CV Revamp + Cover Letter (KES 200)*.\n"
                    . "You’ll get ATS-friendly CV writing, a tailored cover letter, and job application guidance.\n\n"
                    . "👉 Proceed here: https://careershyne.com/order-cv?ref=rd",

                '2' => "✅ You chose *CV from Scratch + Cover Letter (KES 300)*.\n"
                    . "You’ll get a CV crafted from scratch (ATS-optimized), a personalized cover letter, and full job application support.\n\n"
                    . "👉 Proceed here: https://careershyne.com/custom-cv-order?ref=rd",
            };

            return $this->sendMessage($phone, $reply);
        }

        /**
         * CASE 3: Invalid input
         */
        if (!$session) {
            // No session → guide to start with "cv"
            // $reply = "👋 Hello $name!\n\n"
            //     . "To get started, type *cv* and we’ll show you our CV packages.";
            // return $this->sendMessage($phone, $reply);
        }

        // Session exists but input is invalid → optional fallback
        return null;
    }

    /**
     * Helper: Get package list
     */
    private function getPackageList($name)
    {
        $reply  = "Here are our packages (all include FREE CV review and job application support):\n\n";

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
