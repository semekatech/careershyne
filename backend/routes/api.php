<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\CvOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\WhatsapController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::post('/cv-orders', [CvOrderController::class, 'store']);
Route::get('cv-orders/{id}', [CvOrderController::class, 'show']);
Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
Route::post('/callback-confirm', [PaymentController::class, 'confirm']);
Route::post('/payments/status', [PaymentController::class, 'checkStatus'])
    ->name('check.stk.status');

Route::get('industries', [AuthController::class, 'industries']);
Route::get('education-levels', [AuthController::class, 'educationLevels']);
Route::get('counties', [AuthController::class, 'counties']);
Route::post('/log-visitor', function (Request $request) {
    $ip = $request->input('ip', $request->ip()); // ✅ use frontend IP if available
    $userAgent = $request->header('User-Agent');
    $page = $request->input('page', 'unknown');

    \DB::table('visitors')->insert([
        'ip' => $ip,
        'user_agent' => $userAgent,
        'page' => $page,
        'visited_at' => now(),
    ]);



    return response()->json(['message' => 'Visitor logged', 'ip' => $ip, 'page' => $page]);
});
//Dashboard apis
Route::get('/dashboard/stats', [AuthController::class, 'getStats']);
Route::get('/auth/verify-token', [AuthController::class, 'verifyToken']);
Route::get('/me', [AuthController::class, 'userDetails']);
Route::post('/whatsapp-bot', [WhatsapController::class, 'index']);
Route::prefix('orders')->group(function () {
    Route::post('/register', [CvOrderController::class, 'store']);
    Route::post('/update/{id}', [CvOrderController::class, 'update']);
    Route::get('/get/{id}', [CvOrderController::class, 'show']);
    Route::get('/all', [CvOrderController::class, 'fetchAll']);
    Route::put('/{id}/toggle-status', [CvOrderController::class, 'toggleStatus']);
    Route::post('/save', [CvOrderController::class, 'storeOrder']);
    Route::get('/payments', [PaymentController::class, 'fetchPayment']);
    // Route::get('/fetch-all', [CvOrderController::class, 'fetchAll']);
});
Route::prefix('ai')->group(function () {
    Route::post('/upload', [AiController::class, 'uploadCV'])
        ->middleware('throttle:2,1');
    Route::post('/cover-letter', [AiController::class, 'coveletterGenerator'])
        ->middleware('throttle:2,1');
    Route::post('/email-template', [AiController::class, 'emailTemplateGenerator'])
        ->middleware('throttle:2,1');
    Route::post('/cv-revamp', [AiController::class, 'cvRevamp'])
        ->middleware('throttle:2,1');
});
