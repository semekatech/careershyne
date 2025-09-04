<?php

namespace App\Http\Controllers;

use App\Models\CvOrder;
use Illuminate\Http\Request;
use Nette\Utils\Random;
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
        $consumer_key = "lOvMDm6tD3iZ2i28WhdFquTVIeB1bFF2IuVnrW6EEGAUqv0X";
        $consumer_secret = "vPGzRzO6kncITKjS4aGQ1nVsAM3AlJH1PtecEJvWe3cQC1l6jwFXnClKmuvv3540";
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
        curl_setopt($curl, CURLOPT_HTTPHEADER, $stkHeader); //setting custom header
        $BusinessShortCode = "4139507";
        $Timestamp = date("YmdHis");
        // $Timestamp = date('YYYYMMDDHHis');
        $PartyA = $phonenumber;
        $Amount = $amount;
        $CallBackURL = "https://ngumzo.com/callback-confirm?ngumzo_token=37183551";
        $AccountReference = $account_number;
        $TransactionDesc = "Subscription ";
        $Passkey = "5e4e51854f30adce867080e885dc8c19884b0ea2fe6c54fa02f9227dfe9f69da";
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

        info(collect($mpesa_stk));
        info('end of stk');
        return response()->json([
            'success' => true,
            'message' => 'STK Push initiated'
        ]);
    }
}
