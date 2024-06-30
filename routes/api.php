<?php

use App\Http\Controllers\Api\Auth\ForgetPasswordController;
use App\Http\Controllers\Api\Auth\ResetPasswordController;
use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\Auth\ValidateOtpController;
use App\Http\Controllers\Api\Auth\UpdatePasswordController;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Example route definition with middleware
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');

// reset password OTP verification 
Route::post('/forget-password', [ForgetPasswordController::class, 'forgetPassword']);
Route::post('/validate-otp', [ValidateOtpController::class, 'validateOtp']);
Route::post('/reset-password', [UpdatePasswordController::class, 'updatePassword']);
