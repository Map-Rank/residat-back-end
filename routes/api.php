<?php

use App\Models\Zone;
use App\Models\Media;
use App\Service\UtilService;
use Illuminate\Http\Request;
use App\Jobs\WeatherFetchJob;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ZoneController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\FollowController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SectorController;
use App\Http\Controllers\Api\CompanyController;
use App\Http\Controllers\Api\PackageController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WeatherController;
use App\Http\Controllers\Api\FeedbackController;
use App\Http\Controllers\Api\PasswordController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\SubscriptionController;
use App\Http\Controllers\Api\V2\DashboardController;
use App\Http\Controllers\Api\V2\PredictionController;
use App\Http\Controllers\Api\EmailVerificationController;
use App\Http\Controllers\Api\DisasterController as ApiDisasterController;
use App\Http\Controllers\Api\V2\WeatherController as V2WeatherController;

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
    Route::get('/verify-email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware(['signed'])->name('verification.verify.custum');
    Route::post('/email/resend-verification-notification', [EmailVerificationController::class, 'resend'])->name('verification.resend.custum');


    Route::resource('post', PostController::class);
    Route::resource('events', EventController::class);
    Route::get('reports', [ReportController::class, 'index'])->name('list.reports');
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
        Route::get('/', [ApiDisasterController::class, 'index'])->name('disasters.list');
        Route::get('/{disaster}', [ApiDisasterController::class, 'show'])->name('disaster.show');
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

Route::get('/weather-forecast', [V2WeatherController::class, 'getForecast']);
Route::get('/dashboard-weather-forecast', [DashboardController::class, 'getLocationForecast']);
Route::get('/predictions', [PredictionController::class, 'getPredictions']);


Route::middleware(['auth:sanctum',])->group(function () {
    //subscriptions with auth
    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::post('subscriptions', [SubscriptionController::class, 'store'])->name('subscriptions.store');
    Route::put('subscriptions/{subscription}', [SubscriptionController::class, 'update'])->name('subscriptions.update');
    Route::get('subscriptions/current', [SubscriptionController::class, 'currentSubscription'])->name('subscriptions.current');
    Route::patch('subscriptions/{subscription}/cancel', [SubscriptionController::class, 'cancel'])->name('subscriptions.cancel');
    Route::post('subscriptions/{subscription}/renew', [SubscriptionController::class, 'renew'])->name('subscriptions.renew');
    Route::get('subscriptions/history', [SubscriptionController::class, 'history'])->name('subscriptions.history');
    Route::delete('subscriptions/{id}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
});

    //subscriptions without auth
    

    //packages without auth
    Route::get('packages', [PackageController::class, 'index'])->name('packages.index');
    Route::get('packages/{id}', [PackageController::class, 'show'])->name('packages.show');
    Route::post('packages', [PackageController::class, 'store'])->name('packages.store');
    Route::put('packages/{package}', [PackageController::class, 'update'])->name('packages.update');
    Route::delete('packages/{package}', [PackageController::class, 'destroy'])->name('packages.destroy');

    Route::get('weather-test', function(Request $request) {
        ini_set('max_execution_time', 600); // 10 minutes
    
        $allData = [];
        $years = range(2000, 2003);
    
        foreach($years as $year) {
            $start_date = $year . '-01-01';
            $end_date = $year . '-12-31';
    
            $url = "https://archive-api.open-meteo.com/v1/archive";
            $queryParams = http_build_query([
                'latitude' => "10.3430104",
                'longitude' => "15.2498056",
                'start_date' => $start_date,
                'end_date' => $end_date,
                'hourly' => 'relative_humidity_2m,soil_moisture_0_to_7cm',
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum,wind_speed_10m_max',
            ]);
    
            $curl = curl_init("$url?$queryParams");
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_TIMEOUT => 300,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                ],
            ]);
    
            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
    
            if (curl_errno($curl)) {
                $error = curl_error($curl);
                curl_close($curl);
                return [
                    'success' => false,
                    'error' => "Error for year $year: $error",
                ];
            }
    
            curl_close($curl);
    
            if ($httpCode === 200) {
                $responseData = json_decode($response, true);
                
                // Traitement des données quotidiennes
                if (isset($responseData['daily']) && isset($responseData['daily']['time'])) {
                    $dailyData = $responseData['daily'];
                    
                    // Traitement des données horaires pour moyennes journalières
                    $hourlyData = $responseData['hourly'];
                    $hourlyDates = $hourlyData['time'];
                    $humidityData = [];
                    $soilMoistureData = [];
                    
                    // Regrouper les données horaires par jour
                    foreach ($hourlyDates as $index => $hourlyTime) {
                        $date = substr($hourlyTime, 0, 10); // Extraire YYYY-MM-DD
                        if (!isset($humidityData[$date])) {
                            $humidityData[$date] = [];
                            $soilMoistureData[$date] = [];
                        }
                        
                        if (isset($hourlyData['relative_humidity_2m'][$index])) {
                            $humidityData[$date][] = $hourlyData['relative_humidity_2m'][$index];
                        }
                        if (isset($hourlyData['soil_moisture_0_to_7cm'][$index])) {
                            $soilMoistureData[$date][] = $hourlyData['soil_moisture_0_to_7cm'][$index];
                        }
                    }
                    
                    // Combiner les données quotidiennes et les moyennes horaires
                    for ($i = 0; $i < count($dailyData['time']); $i++) {
                        $date = $dailyData['time'][$i];
                        
                        // Calculer les moyennes journalières des données horaires
                        $avgHumidity = !empty($humidityData[$date]) ? array_sum($humidityData[$date]) / count($humidityData[$date]) : null;
                        $avgSoilMoisture = !empty($soilMoistureData[$date]) ? array_sum($soilMoistureData[$date]) / count($soilMoistureData[$date]) : null;
                        
                        $allData[$date] = [
                            'temperature_2m_max' => $dailyData['temperature_2m_max'][$i] ?? null,
                            'temperature_2m_min' => $dailyData['temperature_2m_min'][$i] ?? null,
                            'precipitation_sum' => $dailyData['precipitation_sum'][$i] ?? null,
                            'wind_speed_10m_max' => $dailyData['wind_speed_10m_max'][$i] ?? null,
                            'humidity_mean' => $avgHumidity,
                            'soil_moisture' => $avgSoilMoisture
                        ];
                    }
                }
            } else {
                return [
                    'success' => false,
                    'error' => "HTTP Error for year $year: $httpCode",
                ];
            }
        }
    
        // Créer le dossier s'il n'existe pas
        $directory = storage_path('app/public/weather');
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }
    
        // Création du fichier CSV
        $filePath = storage_path('app/public/weather/weather_'.time().'.csv');
        $fileHeader = ['Date', 'temperature_max', 'temperature_min', 'temperature', 'precipitation', 'wind_speed_max',
            'humidity_mean', 'soil_moisture'];
    
        file_put_contents($filePath, implode(',', $fileHeader) . PHP_EOL);
        
        $file = fopen($filePath, 'a');
        
        // Trier les données par date
        ksort($allData);
    
        foreach($allData as $date => $row) {
            $temp_max = $row['temperature_2m_max'] ?? 0;
            $temp_min = $row['temperature_2m_min'] ?? 0;
            $avgTemperature = ($temp_max + $temp_min) / 2;
            
            $csvRow = [
                $date,  // Date au format YYYY-MM-DD
                $temp_max,
                $temp_min,
                $avgTemperature,
                $row['precipitation_sum'] ?? 0,
                $row['wind_speed_10m_max'] ?? 0,
                $row['humidity_mean'] ?? 0,
                $row['soil_moisture'] ?? 0
            ];
            
            fputcsv($file, $csvRow);
        }
        
        fclose($file);
        
        return [
            'success' => true,
            'final' => $allData,
            'file' => $filePath
        ];
    });

    Route::get('exec', function(Request $request){

        $inputFilePath = base_path('public/csv/weather_data.csv');
        $outputFilePath = base_path('public/csv/clean_weather_data.csv');

        // Path to the Python script
        $pythonScriptPath = base_path('scripts/clean_dataset.py'); // Adjust this path to the location of your Python script

        // Ensure the input file exists
        if (!file_exists($inputFilePath)) {
            return [
                'success' => false,
                'message' => 'Input file not found.',
            ];
        }

        if (!file_exists($pythonScriptPath)) {
            return [
                'success' => false,
                'message' => 'Python file not found.',
            ];
        }

        // Construct the shell command
        $command = escapeshellcmd("python $pythonScriptPath $inputFilePath $outputFilePath");

        // Execute the Python script
        $output = [];
        $returnVar = 0;
        exec($command. " 2>&1", $output, $returnVar);

        // Check for errors during script execution
        if ($returnVar !== 0) {
            return [
                'success' => false,
                'message' => 'Python script execution failed.',
                'output' => $output,
            ];
        }

        // Verify that the cleaned file was created
        if (!file_exists($outputFilePath)) {
            return [
                'success' => false,
                'message' => 'Cleaned file not created.',
            ];
        }

        return [
            'success' => true,
            'message' => 'Weather data cleaned successfully.',
            'output_file' => $outputFilePath,
        ];
        // shell_exec('python scripts/clean_dataset.py public/csv/weather_data.csv public/csv/cleaned_weather_data.csv');
    });
