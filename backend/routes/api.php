<?php
use App\Http\Controllers\CvOrderController;
use Illuminate\Routing\Route;

Route::post('/cv-orders', [CvOrderController::class, 'store']);
