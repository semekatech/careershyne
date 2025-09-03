<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;

Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});
Route::get('/dashboard/stats', [AuthController::class, 'getStats']);
Route::get('/dashboard/promoter/stats', [AuthController::class, 'getPromoterStats']);
Route::get('/notifications/all', [AuthController::class, 'getNotifications']);
// Route::post('/bids/submit', [BidController::class, 'submit']);
// Route::get('/bids/all', [BidController::class, 'getBids']);
// Route::post('/bids/update/{id}', [BidController::class, 'update']);

Route::prefix('bids')->group(function () {
    Route::post('/submit', [BidController::class, 'submit']);
    Route::get('/all', [BidController::class, 'getBids']);
    Route::post('/update/{id}', [BidController::class, 'update']);

    // New approve/reject endpoints
    Route::post('/{id}/approve', [BidController::class, 'approve']);
    Route::post('/{id}/reject', [BidController::class, 'reject']);
});






Route::get('/me', [AuthController::class, 'userDetails']);
Route::post('/update-profile', [AuthController::class, 'updateProfile']);
Route::prefix('campaign')->group(function () {
    Route::post('/register', [CampaignController::class, 'store']);
    Route::post('/update/{id}', [CampaignController::class, 'update']);
    Route::get('/get/{id}', [CampaignController::class, 'show']);
    Route::get('/all', [CampaignController::class, 'index']);
    Route::put('/{id}/toggle-status', [CampaignController::class, 'toggleStatus']);
    Route::get('/fetch-all', [CampaignController::class, 'fetchAll']);
});
Route::get('/auth/verify-token', [AuthController::class, 'verifyToken']);
