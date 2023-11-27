<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\SectorController;

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
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend-verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.resend');
    Route::post('/reset-password', [PasswordController::class, 'reset']);


    Route::get('/posts', [PostController::class, 'index']);
    Route::post('/create', [PostController::class, 'store']);
    Route::get('/show/{id}', [PostController::class, 'show']);
    Route::put('/update/{id}', [PostController::class, 'update']);
    Route::delete('/delete/{id}', [PostController::class, 'destroy']);

    
});


Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('zone', [ZoneController::class, 'index'])->name('zone.index');
Route::get('zone/{id}', [ZoneController::class, 'show'])->name('zone.show');
Route::get('sector', [SectorController::class, 'index'])->name('sector.index');
Route::get('sector/{id}', [SectorController::class, 'show'])->name('sector.show');
Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])->name('password.reset');
