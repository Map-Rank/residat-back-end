<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PermissionController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('roles', [PermissionController::class, 'getAllRolesWithPermissions'])->name('permissions.index');
    Route::get('permissions', [PermissionController::class, 'getAllRolesWithPermissions'])->name('permissions.index');
    Route::get('/role/{id}', [PermissionController::class, 'showRole'])->name('role.show');
    Route::put('/permissions/{id}', [PermissionController::class, 'updatePermissions'])->name('permissions.update');
    Route::get('posts', [PostController::class, 'index'])->name('posts.index');

    //ban user
    Route::post('/users/{id}/ban', [UserController::class, 'banUser'])->name('ban.user');
    //active user
    Route::post('/users/{id}/active', [UserController::class, 'activeUser'])->name('active.user');



    Route::post('/posts/{id}/allow', [PostController::class, 'allowPost'])->name('allow.post');

    //post detail
    Route::post('/post/detail/{id}', [PostController::class, 'show'])->name('post.detail');

    //user detail
    Route::post('/user/detail/{id}', [UserController::class, 'show'])->name('user.detail');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
