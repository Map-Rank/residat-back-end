<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\EmailVerificationController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('zone', [ZoneController::class, 'store']);
    Route::put('zone', [ZoneController::class, 'update']);
    Route::delete('zone', [ZoneController::class, 'delete']);
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend-verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])->name('password.reset');
});


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::post('/reset-password', [PasswordController::class, 'reset']);
Route::get('zone', [ZoneController::class, 'index']);
Route::get('zone/{id}', [ZoneController::class, 'update']);
