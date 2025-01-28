<?php

namespace App\Http\Controllers\Api\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

class DashboardController extends Controller
{
    public function getLocationForecast(Request $request)
    {
        try {
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric'
            ]);

            $yesterday = Carbon::now()->subDay()->format('Y-m-d');
            $threeDaysLater = Carbon::now()->addDays(3)->format('Y-m-d');

            $response = Http::get('https://api.open-meteo.com/v1/forecast', [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'daily' => 'temperature_2m_max,temperature_2m_min,precipitation_sum',
                'timezone' => 'Africa/Douala',
                'start_date' => $yesterday,
                'end_date' => $threeDaysLater
            ]);

            if (!$response->successful()) {
                Log::error('API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Échec de récupération des données météo'
                ], 500);
            }

            $data = $response->json();

            if (!$this->validateResponseData($data)) {
                throw new \Exception('Structure de données invalide dans la réponse');
            }

            $forecast = $this->processForecastData($data['daily']);

            return response()->json([
                'success' => true,
                'timestamp' => Carbon::now()->toDateTimeString(),
                'location' => [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                ],
                'forecast' => $forecast
            ]);

        } catch (\Exception $e) {
            Log::error("Exception dans getLocationForecast: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des données: ' . $e->getMessage()
            ], 500);
        }
    }

    private function validateResponseData(array $data): bool
    {
        return isset($data['daily']) &&
               isset($data['daily']['time']) &&
               isset($data['daily']['temperature_2m_max']) &&
               isset($data['daily']['temperature_2m_min']) &&
               isset($data['daily']['precipitation_sum']);
    }

    private function processForecastData(array $dailyData): array
    {
        $forecast = [];

        for ($i = 0; $i < count($dailyData['time']); $i++) {
            $forecast[] = [
                'date' => $dailyData['time'][$i],
                'temperature' => [
                    'max' => round($dailyData['temperature_2m_max'][$i], 2),
                    'min' => round($dailyData['temperature_2m_min'][$i], 2),
                    'average' => round(($dailyData['temperature_2m_max'][$i] + $dailyData['temperature_2m_min'][$i]) / 2, 2)
                ],
                'precipitation' => round($dailyData['precipitation_sum'][$i], 2)
            ];
        }

        return $forecast;
    }
}
