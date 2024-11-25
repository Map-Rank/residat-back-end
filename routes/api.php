<?php

use App\Models\Zone;
use App\Models\Media;
use App\Service\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SectorController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\DisasterController as ApiDisasterController;
use App\Jobs\WeatherFetchJob;

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

Route::middleware(['auth:sanctum',])->group(function () {
    Route::post('/verify-token', [AuthController::class, 'verifyToken']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->name('verification.verify');
    Route::post('/email/resend-verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.resend');


    Route::resource('post', PostController::class);
    Route::resource('events', EventController::class);
    Route::resource('reports', ReportController::class);
    Route::resource('notifications', NotificationController::class);
    // Route::get('post', [PostController::class, 'index']);
    // Route::post('post', [PostController::class, 'store']);
    // Route::get('/show/{id}', [PostController::class, 'show']);
    // Route::put('/update/{id}', [PostController::class, 'update']);
    // Route::delete('/delete/{id}', [PostController::class, 'destroy']);
    // testascendant
    Route::get('/testascendant', [NotificationController::class, 'testascendant']);

    Route::post('/create-feedback', [FeedbackController::class, 'store']);

    //interactions
    Route::post('post/like/{id}', [PostController::class, 'like']);
    Route::post('post/comment/{id}', [PostController::class, 'comment']);
    Route::post('post/share/{id}', [PostController::class, 'share']);

    Route::get('/profile', [ProfileController::class, 'profile']);
    Route::get('/profile/detail/{id}', [ProfileController::class, 'showProfile']);
    Route::put('/profile/update/{id}', [ProfileController::class, 'update'])->name('update.profile');
    Route::delete('/delete-user', [ProfileController::class, 'destroy'])->name('delete.user');

    Route::get('/profile-interaction', [ProfileController::class, 'interactions']);

    Route::delete('/delete-interaction/{id}', [PostController::class, 'deleteInteraction'])->name('delete.interaction');

    Route::put('/password/update', [PasswordController::class, 'updatePassword']);

    Route::post('follow/{id}', [FollowController::class, 'follow']);
    Route::post('unfollow/{id}', [FollowController::class, 'unfollow']);
    Route::get('followers/{id}', [FollowController::class, 'followers']);
    Route::get('following/{id}', [FollowController::class, 'following']);

    Route::prefix('disasters')->group(function () {
        Route::get('/', [ApiDisasterController::class, 'index']);
        Route::get('/{disaster}', [ApiDisasterController::class, 'show']);
    });
});
//show all post and view one post without auth

Route::get('/get-all-posts', [PostController::class, 'index']);
Route::get('/one-post/{id}', [PostController::class, 'show']);

Route::get('get-all-events', [EventController::class, 'index']);
Route::get('one-event/{id}', [EventController::class, 'show']);

Route::post('login', [AuthController::class, 'login']);
Route::post('register', [AuthController::class, 'register']);
Route::get('zone', [ZoneController::class, 'index'])->name('zone.index');
Route::get('zone/{id}', [ZoneController::class, 'show'])->name('zone.show');
Route::get('sector', [SectorController::class, 'index'])->name('sector.index');
Route::get('sector/{id}', [SectorController::class, 'show'])->name('sector.show');
Route::post('/forgot-password', [PasswordController::class, 'forgotPassword'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset']);
Route::post('/create/request', [CompanyController::class, 'store']);
Route::get('/weather', [WeatherController::class, 'getWeatherData']);

// Route::get('/test-notif', [UtilService::class, 'test']);

Route::get('/test-weather', [UtilService::class, 'test']);
Route::get('weather-test', function(){
    WeatherFetchJob::dispatch(6);
});
