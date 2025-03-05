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
            // Validation des paramètres requis
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
            ]);

            // Définition des dates
            $startDate = $request->input('start_date'); // Hier
            $endDate = $request->input('end_date'); // Dans 3 jours

            // Paramètres de requête
            $queryParams = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'daily' => 'temperature_2m_max,temperature_2m_min,rain_sum',
                'hourly' => 'temperature_2m,relative_humidity_2m,soil_moisture_0_1cm',
                'timezone' => 'Africa/Douala',
                'start_date' => $startDate,
                'end_date' => $endDate
            ];

            // Ajout de vegetation si fourni
            if ($request->has('vegetation')) {
                $queryParams['vegetation'] = $request->vegetation;
            }

            // Requête à l'API météo
            $response = Http::get('https://archive-api.open-meteo.com/v1/archive', $queryParams);

            // Vérification de la réussite de la requête
            if (!$response->successful()) {
                Log::error('API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'queryParams' => $queryParams,
                ]);

                return response()->json([
                    'success' => false,
                    'message' => 'Échec de récupération des données météo'
                ], 500);
            }

            // Conversion en JSON
            $data = $response->json();

            // Ajout de logs pour la réponse de l'API
            Log::info('Données de l\'API', [
                'data' => $data,
            ]);

            // Vérification de la structure des données
            if (!$this->validateResponseData($data)) {
                throw new \Exception('Structure de données invalide dans la réponse');
            }

            // Transformation des données
            $transformedData = $this->transformWeatherData($data, $startDate);

            return response()->json([
                'success' => true,
                'timestamp' => Carbon::now()->toDateTimeString(),
                'location' => [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude
                ],
                'forecast' => $transformedData
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
           isset($data['daily']['rain_sum']);
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

    private function transformWeatherData(array $data, $startDate)
    {
        $formattedData = [];
        $hourlyData = $data['hourly'];

        // Boucle sur chaque jour
        foreach ($data['daily']['time'] as $index => $date) {
            // Récupère les données horaires pour ce jour
            $dailyHumidity = [];
            $dailySoilMoisture = [];
            $dailyTemperature = [];
            
            foreach ($hourlyData['time'] as $hourIndex => $hour) {
                // Si l'heure appartient à ce jour
                if (Carbon::parse($hour)->toDateString() == $date) {
                    // Ajoute les données horaires à la liste pour calculer la moyenne
                    $dailyHumidity[] = $hourlyData['relative_humidity_2m'][$hourIndex] ?? null;
                    $dailySoilMoisture[] = $hourlyData['soil_moisture_0_1cm'][$hourIndex] ?? null;
                    $dailyTemperature[] = $hourlyData['temperature_2m'][$hourIndex] ?? null;
                }
            }

            // Calcul des moyennes
            $avgHumidity = $dailyHumidity ? round(array_sum($dailyHumidity) / count($dailyHumidity), 2) : null;
            $avgSoilMoisture = $dailySoilMoisture ? round(array_sum($dailySoilMoisture) / count($dailySoilMoisture), 2) : null;
            $avgTemperature = $dailyTemperature ? round(array_sum($dailyTemperature) / count($dailyTemperature), 2) : null;

            // Formatage des données
            $formattedData[] = [
                'date' => $date,
                'temp' => $avgTemperature,
                'humidity' => $avgHumidity,
                'soil_moisture' => $avgSoilMoisture,
                'precip' => $data['daily']['rain_sum'][$index] ?? null, // On garde la somme des précipitations pour ce jour
                'vegetation' => null
            ];
        }

        return [
            'target_date' => Carbon::now()->toDateString(),
            'forecast_data' => $formattedData,
            'static_features' => [
                'elevation' => $data['elevation'] ?? null,
                'slope' => null, // Si tu as ces infos ailleurs
                'soil_type' => [], // Ajoute si disponible
            ],
        ];
    }
}
