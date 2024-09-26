<?php

use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ZoneController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\SectorController;

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

Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    Route::get('get-council-user', [UserController::class, 'getUserCouncil'])->name('users.council');
    
    Route::delete('delete/users/{id}', [UserController::class, 'destroy'])->name('users.delete');
    Route::post('/store-user', [UserController::class, 'store'])->name('users.store');
    Route::get('/create-user', [UserController::class, 'create'])->name('users.create');
    Route::get('/edit-user/{id}', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/update-user/{id}', [UserController::class, 'update'])->name('users.update');
    Route::get('roles', [PermissionController::class, 'getAllRolesWithPermissions'])->name('permissions.index');
    Route::get('permissions', [PermissionController::class, 'getAllRolesWithPermissions'])->name('permissions.index');
    Route::get('/role/{id}', [PermissionController::class, 'showRole'])->name('role.show');
    Route::put('/update-permissions/{id}', [PermissionController::class, 'updatePermissions'])->name('permissions.update');

    Route::put('/update-permissions/{id}', [PermissionController::class, 'updateUniqPermission'])->name('update.uniq.permissions');

    Route::post('/create-role', [PermissionController::class, 'store'])->name('create.role');
    Route::put('/update-role/{id}', [PermissionController::class, 'update'])->name('update.role');
    Route::delete('/delete-permission/{id}', [PermissionController::class, 'deletePermission'])->name('delete.permission');
    Route::post('/create-permissions', [PermissionController::class, 'storePermission'])->name('create.permissions');
    Route::get('/all-permissions', [PermissionController::class, 'getAllPermissions'])->name('all.permissions');



    Route::get('posts', [PostController::class, 'index'])->name('posts.index');
    Route::delete('delete/posts/{id}', [PostController::class, 'destroy'])->name('posts.destroy');
    Route::get('zones', [ZoneController::class, 'index'])->name('zones.index');
    Route::get('zones/division/{id}', [ZoneController::class, 'divisions'])->name('region.division');
    Route::get('zones/division/subdivisions/{id}', [ZoneController::class, 'subdivisions'])->name('region.division.subdivisions');
    Route::get('delete/subdivisions/{id}', [ZoneController::class, 'destroy'])->name('delete.subdivision');
    Route::get('create/zone', [ZoneController::class, 'create'])->name('zone.create');
    Route::get('zones/{id}/edit', [ZoneController::class, 'edit'])->name('zone.edit');
    Route::put('zones/{id}', [ZoneController::class, 'update'])->name('zone.update');
    Route::post('zone', [ZoneController::class, 'store'])->name('zone.store');
    Route::resource('reports', ReportController::class);
    Route::resource('sectors', SectorController::class);
    

    Route::get('create/health-report-items', [ReportController::class, 'createHealth'])->name('health-report-items.create');
    Route::post('health-report-items', [ReportController::class, 'healthStore'])->name('health-report-items.store');
    
    Route::get('create/agriculture-report-items', [ReportController::class, 'createAgriculture'])->name('agriculture.report.items.create');
    Route::post('agriculture-report-items', [ReportController::class, 'agricultureStore'])->name('agriculture.report.items.store');

    Route::get('create/infrastructure-report-items', [ReportController::class, 'createInfrastructure'])->name('infrastructure.report.items.create');
    Route::post('infrastructure-report-items', [ReportController::class, 'infrastructureStore'])->name('infrastructure.report.items.store');

    Route::get('create/social-report-items', [ReportController::class, 'createSocial'])->name('social.report.items.create');
    Route::post('social-report-items', [ReportController::class, 'socialStore'])->name('social.report.items.store');

    Route::get('create/food-security-report-items', [ReportController::class, 'createSelectSecurity'])->name('food.security.report.items.create');
    Route::post('food-security-report-items', [ReportController::class, 'selectSecurity'])->name('food.security.report.items.store');

    Route::get('create/ressource-completion-report-items', [ReportController::class, 'createResourceCompletion'])->name('ressource.completion.report.items.create');
    Route::post('ressource-completion-items', [ReportController::class, 'selectSecurity'])->name('ressource.completion.items.store');

    Route::get('create/fishing-vulnerability-report-items', [ReportController::class, 'createFishingVulnerability'])->name('fishing.vulnerability.report.items.create');
    Route::post('fishing-vulnerability-items', [ReportController::class, 'fishingVulnerability'])->name('fishing.vulnerability.items.store');

    Route::get('create/water-stress-report-items', [ReportController::class, 'createWaterStress'])->name('water.stress.report.items.create');
    Route::post('water-stress-items', [ReportController::class, 'waterStress'])->name('water.stress.items.store');

    Route::get('create/migration-report-items', [ReportController::class, 'createMigration'])->name('migration.report.items.create');
    Route::post('migration-items', [ReportController::class, 'migration'])->name('migration.items.store');


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

    Route::resource('feedbacks', FeedbackController::class);
    Route::resource('evenements', EventController::class);

    Route::get('companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('companies/{company}', [CompanyController::class, 'show'])->name('companies.show');
    Route::delete('companies/{company}', [CompanyController::class, 'destroy'])->name('companies.destroy');

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

// Route::get('/add-keys', static function(Request $request){
//     $vectors  = Vector::query()->where('model_type', Report::class)
//         ->where('category', 'FLOOD')->get();
//     $count = 0;

//     foreach($vectors as $vector){
//         $count = $count + 1;
//         $key = VectorKey::query()->insert(
//             [
//                 ['value' => 'keys/comm_very_low.png', 'type' => 'IMAGE', 'name' => 'Very Low','vector_id' => $vector->id,],
//                 ['value' => 'keys/comm_low.png', 'type' => 'IMAGE', 'name' => 'Low','vector_id' => $vector->id,],
//                 ['value' => 'keys/comm_medium.png', 'type' => 'IMAGE', 'name' => 'Medium','vector_id' => $vector->id,],
//                 ['value' => 'keys/comm_high.png', 'type' => 'IMAGE', 'name' => 'High','vector_id' => $vector->id,],
//                 ['value' => 'keys/comm_very_high.png', 'type' => 'IMAGE', 'name' => 'Very High','vector_id' => $vector->id,],
//                 ['value' => 'keys/spatial_very_low.png', 'type' => 'IMAGE', 'name' => 'Very Low','vector_id' => $vector->id,],
//                 ['value' => 'keys/spatial_low.png', 'type' => 'IMAGE', 'name' => 'Low','vector_id' => $vector->id,],
//                 ['value' => 'keys/spatial_medium.png', 'type' => 'IMAGE', 'name' => 'Medium','vector_id' => $vector->id,],
//                 ['value' => 'keys/spatial_high.png', 'type' => 'IMAGE', 'name' => 'High','vector_id' => $vector->id,],
//                 ['value' => 'keys/spatial_very_high.png', 'type' => 'IMAGE', 'name' => 'Very High','vector_id' => $vector->id,],
//                 ['value' => 'keys/sub_divisional_limit.png', 'type' => 'IMAGE', 'name' => 'Subdivisional-Limit','vector_id' => $vector->id,],
//                 ['value' => 'keys/river.png', 'type' => 'IMAGE', 'name' => 'River','vector_id' => $vector->id,],
//                 ['value' => 'keys/river_logone.png', 'type' => 'IMAGE', 'name' => 'River Logone','vector_id' => $vector->id,],
//             ]
//         );
//     }
//     return $count;
// });

require __DIR__.'/auth.php';
