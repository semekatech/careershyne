<?php

use App\Http\Controllers\CvOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

Route::post('/cv-orders', [CvOrderController::class, 'store']);
Route::get('cv-orders/{id}', [CvOrderController::class, 'show']);
Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
Route::post('/callback-confirm', [PaymentController::class, 'confirm']);
Route::post('/payments/status', [PaymentController::class, 'checkStatus'])
    ->name('check.stk.status');


Route::post('/log-visitor', function (Request $request) {
    $ip = $request->ip();
    $userAgent = $request->header('User-Agent');
    $page = $request->input('page', 'unknown');

    \DB::table('visitors')->insert([
        'ip' => $ip,
        'user_agent' => $userAgent,
        'page' => $page,
        'visited_at' => now(),
    ]);

    return response()->json(['message' => 'Visitor logged', 'page' => $page]);
});
