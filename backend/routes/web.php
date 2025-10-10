<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
// Route::get('/', function () {
//     return view('welcome');
// });
Route::get('/', fn() => response()->json(['status' => 'Backend is working']));
Route::prefix('api')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
});
