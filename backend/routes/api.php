<?php

use App\Http\Controllers\AiController;
use App\Http\Controllers\CvOrderController;
use App\Http\Controllers\JobController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\WhatsapController;
use App\Http\Middleware\CheckSubscriptionLimit;
use App\Http\Middleware\LogActivity;
// Route::prefix('api')->group(function () {
//     Route::post('auth/login', [AuthController::class, 'login']);
// });
Route::middleware(LogActivity::class)->group(function () {
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::middleware('auth:api')->post('/profile-setup', [AuthController::class, 'profileSetup']);
    Route::middleware('auth:api')->get('/fetch-profile', [AuthController::class, 'fetchProfile']);
    Route::middleware('auth:api')->post('/update-password', [AuthController::class, 'updatePassword']);
});


Route::post('/cv-orders', [CvOrderController::class, 'store']);
Route::get('cv-orders/{id}', [CvOrderController::class, 'show']);
Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
Route::post('/callback-confirm', [PaymentController::class, 'confirm']);
Route::post('/payments/status', [PaymentController::class, 'checkStatus'])
    ->name('check.stk.status');
Route::middleware('auth:api')->post('/payments/pay', [PaymentController::class, 'initiate']);
Route::get('industries', [AuthController::class, 'industries']);
Route::get('education-levels', [AuthController::class, 'educationLevels']);
Route::get('counties', [AuthController::class, 'counties']);
Route::post('/log-visitor', function (Request $request) {
    $ip = $request->input('ip', $request->ip());
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
Route::get('/profile', [AuthController::class, 'profile']);
Route::middleware('auth:api')->get('/me', [AuthController::class, 'userDetails']);
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
Route::prefix('users')->group(function () {
    Route::post('/register', [CvOrderController::class, 'store']);
    Route::post('/update/{id}', [UserController::class, 'update']);
    Route::get('/get/{id}', [CvOrderController::class, 'show']);
    Route::get('/all', [UserController::class, 'fetchAll']);
    Route::put('/{id}/toggle-status', [CvOrderController::class, 'toggleStatus']);
    Route::post('/save', [UserController::class, 'store']);
    Route::middleware('auth:api')->post('/limits', [UserController::class, 'userLimits']);
    Route::middleware('auth:api')->post('/users/{user}/impersonate', [UserController::class, 'impersonateLogin']);
    Route::get('/usage-activities', [UserController::class, 'UsageActivities']);
});

Route::prefix('jobs')->middleware('auth:api')->group(function () {
    Route::post('/add', [JobController::class, 'store']);

    Route::get('/all', [JobController::class, 'fetchAll']);
    Route::get('/user-jobs', [JobController::class, 'userJobs']);
    Route::post('/check-eligibility', [JobController::class, 'checkEligibility'])
        ->middleware(CheckSubscriptionLimit::class . ':checks');
    Route::post('/cv-revamp', [JobController::class, 'revampCv'])
        ->middleware(CheckSubscriptionLimit::class . ':cv');

    Route::post('/cover-letter', [JobController::class, 'coverLetter'])
        ->middleware(CheckSubscriptionLimit::class . ':coverLetters');

    Route::post('/email-template', [JobController::class, 'emailTemplate'])
        ->middleware(CheckSubscriptionLimit::class . ':emails');

    Route::put('/update/{id}', [JobController::class, 'update']);
});

Route::prefix('ai')->middleware('auth:api')->group(function () {
    Route::post('/upload', [AiController::class, 'uploadCV'])
        ->middleware('throttle:2,1')->middleware(CheckSubscriptionLimit::class . ':cv');
;
    Route::post('/cover-letter', [AiController::class, 'coverletterGenerator'])
        ->middleware('throttle:2,1')->middleware(CheckSubscriptionLimit::class . ':coverLetters');
;
    Route::post('/email-template', [AiController::class, 'emailTemplateGenerator'])
        ->middleware('throttle:2,1')->middleware(CheckSubscriptionLimit::class . ':emails');
;
    Route::post('/cv-revamp', [AiController::class, 'cvRevamp'])
        ->middleware('throttle:2,1')->middleware(CheckSubscriptionLimit::class . ':cv');
;
});

Route::get('/status', function () {
    return response()->json(['status' => 'API is live']);
});

});
