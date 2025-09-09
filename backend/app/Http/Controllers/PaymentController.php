<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use App\Models\MpesaPayment;
use Illuminate\Http\Request;
use Nette\Utils\Random;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderMail;
use DB;

class PaymentController extends Controller
{

    public function initiate(Request $request)
    {
        $validated = $request->validate([
            'phone'   => 'required|string|regex:/^254\d{9}$/',
            'amount'  => 'required|numeric|min:1',
        ]);
        $amount = $request->input('amount');
        $plan = $request->input('plan');
        $account_number = $request->input('orderID');
        $phone1 = $validated['phone'];
        $phone2 = (int) $phone1;
        $first = substr($phone2, 0, 3);
        if ($first != 254) {
            $phonenumber = "254" . $phone2;
        } elseif (substr($phone1, 0, 3) == 254) {
            $phonenumber = $phone1;
        }
        date_default_timezone_set("Africa/Nairobi");
        $consumer_key = "mqnWvo8e5l3kpmrYWjCqBTs8w44H7zm73PPDTWINAgcQBKtL";
        $consumer_secret = "3tN8JdOmqH6pzkiHjujcTjQit7t0r2HNNU4F9ry7hWRpfhdGvFeqNb9g2cXaILzt";
        $access_token_url =
            "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $access_token_url);
        $credentials = base64_encode($consumer_key . ":" . $consumer_secret);
        curl_setopt($curl, CURLOPT_HTTPHEADER, [
            "Authorization: Basic " . $credentials,
        ]); //setting a custom header
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $curl_response = curl_exec($curl);
        $access_token = json_decode($curl_response)->access_token;
        curl_close($curl);
        // initiating the transaction
        $initiate_url =
            "https://api.safaricom.co.ke/mpesa/stkpush/v1/processrequest";
        $stkHeader = [
            "Content-Type:application/json",
            "Authorization:Bearer " . $access_token,
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $initiate_url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkHeader);
        $BusinessShortCode = "4167323";
        $Timestamp = date("YmdHis");
        // $Timestamp = date('YYYYMMDDHHis');
        $PartyA = $phonenumber;
        $Amount = $amount;
        $CallBackURL = "https://careershyne.com/api/callback-confirm?ngumzo_token=37183551";
        $AccountReference = $account_number;
        $TransactionDesc = "Subscription ";
        $Passkey = "86b62107f93c1a1d013ab36ab83cd12aca4e6b3f9fd3778c8d5422178bde52a8";
        $Password = base64_encode($BusinessShortCode . $Passkey . $Timestamp);
        $curl_post_data = [
            // Fill in the request parameters with valid values
            "BusinessShortCode" => $BusinessShortCode,
            "Password" => $Password,
            "Timestamp" => $Timestamp,
            "TransactionType" => "CustomerPayBillOnline",
            "Amount" => $Amount,
            "PartyA" => $PartyA,
            "PartyB" => $BusinessShortCode,
            "PhoneNumber" => $PartyA,
            "CallBackURL" => $CallBackURL,
            "AccountReference" => $AccountReference,
            "TransactionDesc" => $TransactionDesc,
        ];
        $data_string = json_encode($curl_post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
        $curl_response = curl_exec($curl);
        // store feedback start
        $resp_data = json_decode($curl_response);
        info('stk data' . collect($resp_data));
        $MerchantRequestID = $resp_data->MerchantRequestID;
        $CheckoutRequestID = $resp_data->CheckoutRequestID;
        $ResponseCode = $resp_data->ResponseCode;
        $ResponseDescription = $resp_data->ResponseDescription;
        curl_close($curl);
        $mpesa_stk = DB::table('mpesa_stks')->insert([
            "merchant_request_id" => $MerchantRequestID,
            "checkout_request_id" => $CheckoutRequestID,
            "phone_number" => $PartyA,
            "resultDescription" => $ResponseDescription ?? 'No Response',
            "resultCode" => $ResponseCode ?? '13',
            "plan_id" => $account_number,
            "plan" =>  $account_number,
            "user_id" =>  $account_number,
            "created_at" => now(),
            "updated_at" => now(),
            "status" => 0,
        ]);
        return response()->json([
            'reference' => $CheckoutRequestID,
            'success' => true,
            'message' => 'STK Push initiated'
        ]);
    }

    public function confirm(Request $request)
    {
        $ngumzoToken = $request->query('ngumzo_token');
        if (!$ngumzoToken) {
            info('Ngumzo token is missing');
            return response()->json(['error' => 'token is missing'], 400);
        }

        $failed = 0;
        info('confirmation started');
        $data = file_get_contents("php://input");
        $content = json_decode($request->getContent());
        $data = json_decode($data);
        $stkCallback = $data->Body->stkCallback;
        $MerchantRequestID = $stkCallback->MerchantRequestID;
        $CheckoutRequestID = $stkCallback->CheckoutRequestID;
        $ResultCode = $stkCallback->ResultCode;
        $ResultDesc = $stkCallback->ResultDesc;
        if (empty($stkCallback->CallbackMetadata)) {
            $order_detail = DB::table('mpesa_stks')
                ->where('merchant_request_id', $MerchantRequestID)
                ->first();
            $mpesa_stk =  DB::table('mpesa_stks')->where('id', $order_detail->id);
            $mpesa_stk->update(['status' => 2, 'resultCode' => $ResultCode, 'ResultDescription' => $ResultDesc]);
            $orderID = $order_detail->plan_id;
            $order = DB::table('cv_orders')
                ->where('orderID', $orderID)
                ->first();
            $url = "https://careershyne.com/payment/" . $orderID;
            $message = "Your order of #{$order->orderID} has not been processed. Kindly click {$url} to finalize the order.";
            $subject = 'Order Confirmation';

            $details = [
                'subject' => $subject,
                'message' => $message,
                'order'   => $order,
            ];
            Mail::to($order->email)->send(new OrderMail($details));
        } else {
            $CallbackMetadata = $stkCallback->CallbackMetadata;
            echo "Two /";
            $Amount = null;
            $MpesaReceiptNumber = null;
            $Balance = null;
            $TransactionDate = null;
            $PhoneNumber = null;
            info(collect($data));
            foreach ($CallbackMetadata as $dt) {
                foreach ($dt as $k) {
                    $j = 0;
                    $key = null;
                    foreach ($k as $x) {
                        #echo " // ";
                        if ($j == 0) {
                            $key = $x;
                        } elseif ($j == 1) {
                            if ($key == "Amount") {
                                $Amount = $x;
                            }
                            if ($key == "MpesaReceiptNumber") {
                                $MpesaReceiptNumber = $x;
                            }
                            if ($key == "Balance") {
                                $Balance = $x;
                            }
                            if ($key == "TransactionDate") {
                                $TransactionDate = $x;
                            }
                            if ($key == "PhoneNumber") {
                                $PhoneNumber = $x;
                            }
                        }
                        $j++;
                    }
                }
            }
            info('amount' . $Amount);
            $formattedContact  = str_replace("254", "0", $PhoneNumber);
            if ($Amount > 0) {
                info($PhoneNumber . 'paid' . $Amount);
                $order_details = DB::table('mpesa_stks')
                    ->where('merchant_request_id', $MerchantRequestID)
                    ->first();
                $orderID = $order_details->plan_id;
                $userID = $order_details->user_id;
                $data = [
                    'amount' => $Amount,
                    'mpesa_receipt_number' => $MpesaReceiptNumber,
                    'transaction_date' => now(),
                    'phonenumber' => $PhoneNumber,
                    'plan_id' => $orderID,
                    'user_id' => $userID,
                ];
                // info('sd'.$MerchantRequestID);
                // Check if payment already exists for this request
                $existingPayment = MpesaPayment::where('plan_id', $orderID)->exists();
                if (!$existingPayment && MpesaPayment::create($data)) {
                    $mpesa_stk =  DB::table('mpesa_stks')->where('id', $order_details->id)->first();
                    DB::table('mpesa_stks')
                        ->where('id', $order_details->id)
                        ->update([
                            'status' => 1,
                            'resultCode' => $ResultCode,
                            'ResultDescription' => $ResultDesc,
                        ]);

                    DB::table('cv_orders')
                        ->where('orderID', $orderID)
                        ->update([
                            'status' => 'paid',
                        ]);
                    $order = DB::table('cv_orders')
                        ->where('orderID', $orderID)
                        ->first();
                    $message2 = "Your order of #{$order->orderID} has been processed successfully. The order will be delivered within 24 hours.";
                    $subject = 'Order Confirmation';

                    $details = [
                        'subject' => $subject,
                        'message' => $message2,
                        'order'   => $order,
                    ];
                    // Build base message
                    $message = "📢 New CV Order Alert!\n\n";
                    $message .= "🆔 Order ID: #{$order->orderID}\n";
                    $message .= "👤 Full Name: {$order->fullname}\n";
                    $message .= "📧 Email: {$order->email}\n";
                    $message .= "📞 Phone: {$order->phone}\n";
                    $message .= "📌 Status: {$order->status}\n";

                    // If type = customization
                    if ($order->type === 'cv') {
                        $message .= "\n📝 Order Type: CV Customization\n";
                        $message .= "📂 Uploaded CV: https://careershyne.com/storage/{$order->cv_path}\n";

                        $message .= "💰 Amount: {$order->amount}\n";
                    }

                    // If type = cvscratch (from scratch)
                    if ($order->type === 'cvscratch') {
                        $message .= "\n📝 Order Type: CV From Scratch + Cover Letter\n";
                        $message .= "📍 Location: {$order->location}\n";
                        $message .= "🎯 Career Goal: {$order->career_goal}\n";

                        // Education
                        $education = json_decode($order->education, true);
                        if ($education) {
                            $message .= "\n🎓 Education:\n";
                            foreach ($education as $edu) {
                                $message .= "- {$edu['degree']} at {$edu['institution']} ({$edu['startDate']} – {$edu['endDate']})\n";
                            }
                        }

                        // Experience
                        $experience = json_decode($order->experience, true);
                        if ($experience) {
                            $message .= "\n💼 Work Experience:\n";
                            foreach ($experience as $exp) {
                                $message .= "- {$exp['title']} at {$exp['company']} ({$exp['startDate']} – {$exp['endDate']})\n  Responsibilities: {$exp['responsibilities']}\n";
                            }
                        }

                        // Skills
                        if (!empty($order->skills)) {
                            $message .= "\n⚡ Skills: {$order->skills}\n";
                        }

                        // Certifications
                        $certs = json_decode($order->certifications, true);
                        if ($certs) {
                            $message .= "\n📜 Certifications:\n";
                            foreach ($certs as $cert) {
                                if (!empty($cert['name'])) {
                                    $message .= "- {$cert['name']} ({$cert['issuer']}, {$cert['year']})\n";
                                }
                            }
                        }

                        // Links
                        if (!empty($order->linkedin)) {
                            $message .= "\n🔗 LinkedIn: {$order->linkedin}\n";
                        }
                        if (!empty($order->portfolio)) {
                            $message .= "🌐 Portfolio: {$order->portfolio}\n";
                        }

                        // Cover Letter
                        $message .= "\n📄 Cover Letter:\n";
                        $message .= "🎯 Target Role: {$order->cover_role}\n";
                        $message .= "💡 Why Interested: {$order->cover_why}\n";
                        $message .= "💪 Strengths: {$order->cover_strengths}\n";

                        $message .= "💰 Amount: {$order->amount}\n";
                    }



                    $admindetails = [
                        'subject' => "New Order Alert",
                        'message' => nl2br($message),
                        'order'   => $order,
                    ];
                    $this->sendMessage('254108850348', '254705030613', $message);
                    $this->sendMessage('254108850348', '254758428993', $message);
                    Mail::to($order->email)->send(new OrderMail($details));
                    Mail::to(['georgemuemah@gmail.com', 'nancymunee@gmail.com'])->send(new OrderMail($admindetails));
                }
            } else {
            }
        }
    }

    public function sendMessage($sender, $to, $message)
    {
        $apiUrl = 'https://ngumzo.com/v1/send-message';
        $apiKey = 'qkv0Da9MPwOCeCyPOpnh2JR5QEJkw1';



        // Prepare the data for the POST request
        $data = [
            'sender' => $sender,
            'number' => $to,
            'message' => $message // The welcome message
        ];

        // Initialize cURL session
        $ch = curl_init($apiUrl);

        // Set the cURL options
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

        // Optionally log the response for debugging
        error_log('Response: ' . $response);

        // Close the cURL session
        curl_close($ch);
    }

    public function checkStatus(Request $request)
    {
        $transactionId = $request->input('track_link');
        $paymentStatus = DB::table('mpesa_stks')
            ->where('checkout_request_id', $transactionId)
            ->orderBy('created_at', 'desc')
            ->first();
        if ($paymentStatus) {
            $status = $paymentStatus->status;
            $message = $paymentStatus->resultDescription;
        } else {
            $status = 7;
            $message = 'Failed to initiate STK';
        }
        info($status);
        return response()->json([
            'status' => $status,
            'message' => $message,
        ]);
    }


    public function fetchPayment(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $orders =MpesaPayment::orderByDesc('id')->get();
        return response()->json(['orders' => $orders], 200);
    }
}
