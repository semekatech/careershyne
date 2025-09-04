<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\CvOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\PaymentController;

Route::post('/cv-orders', [CvOrderController::class, 'store']);
Route::get('cv-orders/{id}', [CvOrderController::class, 'show']);
Route::post('/payments/initiate', [PaymentController::class, 'initiate']);
