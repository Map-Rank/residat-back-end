<?php

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::post('/store-user', [UserController::class, 'store'])->name('users.store');
    Route::get('/create-user', [UserController::class, 'create'])->name('users.create');
    Route::get('/edit-user/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/update-user/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('roles', [PermissionController::class, 'getAllRolesWithPermissions'])->name('permissions.index');
    Route::get('permissions', [PermissionController::class, 'getAllRolesWithPermissions'])->name('permissions.index');
    Route::get('/role/{id}', [PermissionController::class, 'showRole'])->name('role.show');
    Route::put('/permissions/{id}', [PermissionController::class, 'updatePermissions'])->name('permissions.update');

    Route::put('/update-permissions/{id}', [PermissionController::class, 'updateUniqPermission'])->name('update.uniq.permissions');

    Route::post('/create-role', [PermissionController::class, 'store'])->name('create.role');
    Route::put('/update-role/{id}', [PermissionController::class, 'update'])->name('update.role');
    Route::put('/delete-permission/{id}', [PermissionController::class, 'deletePermission'])->name('delete.permission');
    Route::post('/create-permissions', [PermissionController::class, 'storePermission'])->name('create.permissions');
    Route::get('/all-permissions', [PermissionController::class, 'getAllPermissions'])->name('all.permissions');



    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::get('zones', [ZoneController::class, 'index'])->name('zones.index');
    Route::get('zones/division/{id}', [ZoneController::class, 'divisions'])->name('region.division');
    Route::get('zones/division/subdivisions/{id}', [ZoneController::class, 'subdivisions'])->name('region.division.subdivisions');
    Route::get('delete/subdivisions/{id}', [ZoneController::class, 'destroy'])->name('delete.subdivision');
    Route::get('create/zone', [ZoneController::class, 'create'])->name('zone.create');
    Route::get('zones/{id}/edit', [ZoneController::class, 'edit'])->name('zone.edit');
    Route::put('zones/{id}', [ZoneController::class, 'update'])->name('zone.update');
    Route::post('zone', [ZoneController::class, 'store'])->name('zone.store');
    Route::resource('reports', ReportController::class);
    Route::get('create/health-report-items', [ReportController::class, 'createHealth'])->name('health-report-items.create');
    Route::post('health-report-items', [ReportController::class, 'healthStore'])->name('health-report-items.store');


    //ban user
    Route::post('/users/{id}/ban', [UserController::class, 'banUser'])->name('ban.user');
    //active user
    Route::post('/users/{id}/active', [UserController::class, 'activeUser'])->name('active.user');



    Route::post('/posts/{id}/allow', [PostController::class, 'allowPost'])->name('allow.post');

    //post detail
    Route::get('/post/{id}/detail', [PostController::class, 'show'])->name('post.detail');

    //user detail
    Route::get('/user/{id}/detail', [UserController::class, 'show'])->name('user.detail');


    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

});
Route::get('/get-token-from-session', [AuthenticatedSessionController::class, 'getTokenFromSession'])->name('token');

Route::get('/send-mail', static function(){
    Log::info('sprint');
    $toEmail = 'keuleuronald@gmail.com';
    $subject = 'Zip File Attachment';
    $info = 'Please find the attached zip file.';

    Mail::send([], [], function ($message) use ($toEmail, $subject, $info) {
        $message->to($toEmail)
            ->subject($subject)
            ->html($info);
    });
});

Route::get('/add-keys', static function(Request $request){
    $vectors  = Vector::query()->where('model_type', Report::class)
        ->where('category', $request->input('type'))->get();
    $count = 0;
    foreach($vectors as $vector){
        $count = $count + 1;
        $key = DB::table('vector_keys')->insert(
            ['value' => 'keys/village.png', 'type' => 'IMAGE', 'name' => 'Village','vector_id' => $vector->id,],
            ['value' => 'keys/sub_divisional_limit.png', 'type' => 'IMAGE', 'name' => 'Subdivisional-Limit','vector_id' => $vector->id,],
            ['value' => 'keys/extinct_stream.png', 'type' => 'IMAGE', 'name' => 'Extinct Stream','vector_id' => $vector->id,],
            ['value' => 'keys/4_month_stream.png', 'type' => 'IMAGE', 'name' => '4 months Stream flow','vector_id' => $vector->id,],
            ['value' => 'keys/8_month_stream.png', 'type' => 'IMAGE', 'name' => '8 months flow','vector_id' => $vector->id,],
            ['value' => 'keys/10_month_stream.png', 'type' => 'IMAGE', 'name' => '10 months Stream flow','vector_id' => $vector->id,],
            ['value' => 'keys/permanent.png', 'type' => 'IMAGE', 'name' => 'Permanent','vector_id' => $vector->id,],
            ['value' => 'keys/river.png', 'type' => 'IMAGE', 'name' => 'River','vector_id' => $vector->id,],
            ['value' => 'keys/river_logone.png', 'type' => 'IMAGE', 'name' => 'River Logone','vector_id' => $vector->id,],
        );
    }
    return $count;
});

require __DIR__.'/auth.php';
