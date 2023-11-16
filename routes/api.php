<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ZoneController;

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
});

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('zone', [ZoneController::class, 'index']);
Route::get('zone/{id}', [ZoneController::class, 'update']);
