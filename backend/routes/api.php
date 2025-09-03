<?php
use App\Http\Controllers\CvOrderController;

Route::post('/cv-orders', [CvOrderController::class, 'store']);
