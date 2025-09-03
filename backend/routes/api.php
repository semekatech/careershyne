<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BidController;
use App\Http\Controllers\CvOrderController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CampaignController;

Route::post('/cv-orders', [CvOrderController::class, 'store']);
