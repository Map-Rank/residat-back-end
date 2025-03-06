<?php

namespace App\Http\Controllers\Api\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class WeatherController extends Controller
{
    private array $locations = [
        'Gobo' => ['latitude' => 10.0507, 'longitude' => 15.4014],
        'Yagoua' => ['latitude' => 10.3411, 'longitude' => 15.2329],
        'Wina' => ['latitude' => 10.1833, 'longitude' => 15.2333],
        'Kai-kai' => ['latitude' => 10.2833, 'longitude' => 15.2833],
        'Maga' => ['latitude' => 10.8333, 'longitude' => 14.9333],
        'Gueme' => ['latitude' => 10.2833, 'longitude' => 15.2333],
        'Kalfou' => ['latitude' => 10.2833, 'longitude' => 14.9333],
        'Kar-hay' => ['latitude' => 10.2833, 'longitude' => 15.2833],
        'Datcheka' => ['latitude' => 10.3333, 'longitude' => 15.2333],
    ];

    public function getForecast()
    {
        $forecasts = [];
        $currentDate = Carbon::now();

        foreach ($this->locations as $city => $coordinates) {
            try {
                Log::info("Fetching data for {$city}", $coordinates);

                $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $coordinates['latitude'],
                    'longitude' => $coordinates['longitude'],
                    'hourly' => 'temperature_2m,rain,soil_moisture_0_1cm',
                    'timezone' => 'Africa/Douala',
                    'forecast_days' => 5
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    
                    if ($this->validateResponseData($data)) {
                        $forecasts[$city] = [
                            'coordinates' => $coordinates,
                            'daily_summary' => $this->calculateDailySummary($data['hourly'])
                        ];
                    } else {
                        throw new \Exception('Structure de données invalide dans la réponse');
                    }
                } else {
                    Log::error("API Error for {$city}", [
                        'status' => $response->status(),
                        'body' => $response->body(),
                    ]);

                    $forecasts[$city] = [
                        'error' => 'Échec de récupération des données pour ' . $city,
                        'status' => $response->status(),
                        'details' => $response->body()
                    ];
                }
            } catch (\Exception $e) {
                Log::error("Exception for {$city}: " . $e->getMessage());
                
                $forecasts[$city] = [
                    'error' => 'Erreur lors de la récupération des données: ' . $e->getMessage()
                ];
            }

            usleep(100000); // 100ms pause entre les requêtes
        }

        return response()->json([
            'timestamp' => $currentDate->toDateTimeString(),
            'forecasts' => $forecasts
        ]);
    }

    private function validateResponseData(array $data): bool
    {
        return isset($data['hourly']) &&
               isset($data['hourly']['time']) &&
               isset($data['hourly']['temperature_2m']) &&
               isset($data['hourly']['rain']) &&
               isset($data['hourly']['soil_moisture_0_1cm']);
    }

    private function calculateDailySummary(array $hourlyData): array
    {
        $dailySummary = [];
        $currentDate = null;
        $tempSum = 0;
        $rainSum = 0;
        $soilMoistureSum = 0;
        $count = 0;

        for ($i = 0; $i < count($hourlyData['time']); $i++) {
            $date = substr($hourlyData['time'][$i], 0, 10); // Extract YYYY-MM-DD

            if ($currentDate !== $date) {
                if ($currentDate !== null) {
                    $dailySummary[] = [
                        'date' => $currentDate,
                        'average_temperature' => round($tempSum / $count, 2),
                        'total_rain' => round($rainSum, 2),
                        'average_soil_moisture' => round($soilMoistureSum / $count, 3)
                    ];
                }
                
                $currentDate = $date;
                $tempSum = 0;
                $rainSum = 0;
                $soilMoistureSum = 0;
                $count = 0;
            }

            $tempSum += $hourlyData['temperature_2m'][$i];
            $rainSum += $hourlyData['rain'][$i];
            $soilMoistureSum += $hourlyData['soil_moisture_0_1cm'][$i];
            $count++;
        }

        // Add the last day
        if ($count > 0) {
            $dailySummary[] = [
                'date' => $currentDate,
                'average_temperature' => round($tempSum / $count, 2),
                'total_rain' => round($rainSum, 2),
                'average_soil_moisture' => round($soilMoistureSum / $count, 3)
            ];
        }

        return $dailySummary;
    }
}
