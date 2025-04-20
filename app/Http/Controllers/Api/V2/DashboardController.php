<?php

namespace App\Http\Controllers\Api\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use App\Models\Prediction;

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

            // Paramètres de requête
            $queryParams = [
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'daily' => 'temperature_2m_max,temperature_2m_min,rain_sum',
                'hourly' => 'temperature_2m,relative_humidity_2m,soil_moisture_0_1cm',
                'timezone' => 'Africa/Douala',
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            ];

            // Requête à l'API météo
            $response = Http::get('https://api.open-meteo.com/v1/forecast', $queryParams);

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

            $data = $response->json();

            // Transformation des données météorologiques
            $weatherData = [
                'date' => $data['daily']['time'],
                'temp_max' => $data['daily']['temperature_2m_max'],
                'temp_min' => $data['daily']['temperature_2m_min'],
                'precipitation' => $data['daily']['rain_sum'],
                'soil_moisture' => array_map(function($values) {
                    return array_sum($values) / count($values);
                }, array_chunk($data['hourly']['soil_moisture_0_1cm'], 24))
            ];

            // Données géographiques statiques
            $geoData = [
                'location' => ['Yagoua'],
                'elevation' => [$data['elevation'] ?? 330],
                'slope' => [3],
                'soil_type' => ['silty']
            ];

            // Génération des données hydrologiques simulées
            $dates = $data['daily']['time'];
            $numDays = count($dates);
            
            // Valeurs de base pour les données hydrologiques
            $baseRiverFlow = 110.0;
            $baseGroundwater = 43.0;
            $baseReservoir = 145.0;
            
            $hydroData = [
                'measurement_date' => $dates,
                'river_flow' => array_map(function($i) use ($baseRiverFlow) {
                    return round($baseRiverFlow - ($i * 0.5) + (rand(-10, 10) / 10), 1);
                }, range(0, $numDays - 1)),
                'groundwater_level' => array_map(function($i) use ($baseGroundwater) {
                    return round($baseGroundwater - ($i * 0.2) + (rand(-5, 5) / 10), 1);
                }, range(0, $numDays - 1)),
                'reservoir_level' => array_map(function($i) use ($baseReservoir) {
                    return round($baseReservoir - ($i * 0.4) + (rand(-8, 8) / 10), 1);
                }, range(0, $numDays - 1)),
                'location' => array_fill(0, $numDays, 'Yagoua')
            ];

            return response()->json([
                'success' => true,
                'weather_data' => $weatherData,
                'geo_data' => $geoData,
                'hydro_data' => $hydroData
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

            // Formatage des données avec la nouvelle structure
            $formattedData[] = [
                'date' => $date,
                'precip' => round($data['daily']['rain_sum'][$index] ?? 0, 2),
                'temp' => $avgTemperature ?? 0,
                'humid' => $avgHumidity ?? 0,
                'soil_m' => $avgSoilMoisture ?? 0,
                'vegetation' => round(rand(50, 70) / 100, 2) // Valeur aléatoire entre 0.50 et 0.70 pour l'exemple
            ];
        }

        return [
            'target_date' => $startDate,
            'forecast_data' => $formattedData,
            'static_features' => [
                'elevation' => $data['elevation'] ?? 245.6,
                'slope' => 3.8, // Valeur fixe pour l'exemple
                'soil_type' => [0, 0, 1] // Format simplifié comme demandé
            ],
        ];
    }

    public function predict(Request $request)
    {
        try {
            // Validation des paramètres requis
            $request->validate([
                'latitude' => 'required|numeric',
                'longitude' => 'required|numeric',
                'start_date' => 'required|date_format:Y-m-d',
                'end_date' => 'required|date_format:Y-m-d',
                'zone_id' => 'required|exists:zones,id'
            ]);

            // Récupérer les données météo
            $weatherResponse = $this->getLocationForecast($request);
            $weatherData = json_decode($weatherResponse->getContent(), true);

            

            if (!$weatherData['success']) {
                throw new \Exception('Échec de récupération des données météo');
            }

            // Supprimer la clé 'success' et envoyer le reste des données directement
            unset($weatherData['success']);

            // Renommer les clés pour correspondre au format attendu
            $predictionPayload = [
                'weather' => $weatherData['weather_data'],
                'geo' => $weatherData['geo_data'],
                'hydro' => $weatherData['hydro_data']
            ];
            
            // Requête à l'API de prédiction
            $response = Http::withHeaders([
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ])->post('https://residat-model-0f084a12a18c.herokuapp.com/predict', $predictionPayload);

            // dd($response);

            if (!$response->successful()) {
                Log::error('Prediction API Error', [
                    'status' => $response->status(),
                    'body' => $response->body(),
                    'sent_data' => $predictionPayload
                ]);

                throw new \Exception('Échec de la prédiction');
            }

            $responseData = $response->json();
            $predictionData = $responseData['predictions'];

            $estimates = $this->estimateReservoirTrends(
                ($predictionData[0]["reservoir_7d"] + $predictionData[0]["reservoir_7d_change"]),
                $predictionData[0]["reservoir_7d"], 
                $predictionData[0]["reservoir_14d"]);

            return $estimates;
            // Création de l'entrée de prédiction dans la base de données
            $predictionArray = [
                'zone_id' => $request->input('zone_id'),
                'date' => $request->input('start_date'),
                'prediction_data' => $responseData
            ];

            $prediction = Prediction::create($predictionArray);

            return response()->json([
                'success' => true,
                'prediction' => $responseData,
                'prediction_record' => $prediction
            ]);

        } catch (\Exception $e) {
            Log::error("Exception dans predict: " . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la prédiction: ' . $e->getMessage()
            ], 500);
        }
    }


    /**
     * Estimates 1-day to 5-day reservoir levels and changes based on 7-day and 14-day predictions.
     * 
     * @param float $currentLevel      Current reservoir level (%).
     * @param float $reservoir7d       7-day predicted reservoir level (%).
     * @param float $reservoir14d      14-day predicted reservoir level (%).
     * @return array                   Array containing 1-5 day estimates (levels + changes).
     */
    function estimateReservoirTrends(float $currentLevel, float $reservoir7d, float $reservoir14d): array {
        // Validate inputs
        if ($currentLevel <= 0 || $reservoir7d <= 0 || $reservoir14d <= 0) {
            throw new InvalidArgumentException("All reservoir levels must be positive values.");
        }

        // Calculate daily change rates
        $dailyChange7d = ($reservoir7d - $currentLevel) / 7;
        $dailyChange14d = ($reservoir14d - $currentLevel) / 14;

        // Weighted average (favors 7-day trend more heavily)
        $weightedDailyChange = (0.7 * $dailyChange7d) + (0.3 * $dailyChange14d);

        $estimates = [];
        for ($days = 1; $days <= 5; $days++) {
            // Linear estimation (simple interpolation)
            $levelLinear = $currentLevel + ($dailyChange7d * $days);
            $changeLinear = ($levelLinear - $currentLevel) / $currentLevel;

            // Weighted estimation (more stable)
            $levelWeighted = $currentLevel + ($weightedDailyChange * $days);
            $changeWeighted = ($levelWeighted - $currentLevel) / $currentLevel;

            $estimates[$days] = [
                'linear' => [
                    'level' => round($levelLinear, 2),
                    'change' => round($changeLinear, 4)
                ],
                'weighted' => [
                    'level' => round($levelWeighted, 2),
                    'change' => round($changeWeighted, 4)
                ]
            ];
        }

        return $estimates;
    }

}
