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

class UserController extends Controller
{

  public function fetchAll(Request $request)
    {
        $token = $request->bearerToken();
        $user = User::where('api_token', hash('sha256', $token))->where('role', '1109')->first();
        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }
        $users = User::orderByDesc('id')->get();
        return response()->json(['users' => $users], 200);
    }

}
