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

            // dd($response->json());

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
                'timestamp' => $request->input('start_date'),
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
            // Récupérer les données météo
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            
            $results = [];
           
            for($i = 0; $i < 5; $i++){
                
                $request->merge(['start_date' => Carbon::parse($startDate)->addDay($i)->format('Y-m-d')]);
                $request->merge(['end_date' =>  Carbon::parse($endDate)->addDay($i)->format('Y-m-d')]);
            
        
                $weatherResponse = $this->getLocationForecast($request);
                
                $weatherData = json_decode($weatherResponse->getContent(), true);
                dd($weatherData['success']);

                
                

                // Vérifier si la récupération des données météo a réussi
                if (!$weatherData['success']) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Échec de récupération des données météo'
                    ], 500);
                }

                // Préparer les données dans le format exact attendu par l'API
                $predictionPayload = [
                    'target_date' => $weatherData['forecast']['target_date'],
                    'forecast_data' => $weatherData['forecast']['forecast_data'],
                    'static_features' => $weatherData['forecast']['static_features']
                ];

                // Log des données avant envoi pour debug
                Log::info('Données envoyées à l\'API de prédiction', [
                    'payload' => $predictionPayload
                ]);

                // Faire la requête à l'API de prédiction
                $response = Http::withHeaders([
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json'
                ])->post('https://residat-flood-drought-model-514c88923a4c.herokuapp.com/predict', $predictionPayload);

                // Log de la réponse pour debug
                Log::info('Réponse de l\'API', [
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);

                // Vérifier si la requête a réussi
                if (!$response->successful()) {
                    Log::error('Prediction API Error', [
                        'status' => $response->status(),
                        'body' => $response->body(),
                        'sent_data' => $predictionPayload
                    ]);

                    return response()->json([
                        'success' => false,
                        'message' => 'Échec de la prédiction',
                        'error' => $response->body()
                    ], 500);
                }

                $droughtRisk = $response->json()["drought_risk"];
                $floodRisk = $response->json()["flood_risk"];

                // Convert risk to percentages (capped at 100%)
                $floodRiskPercent = min(($floodRisk / 1e-8) * 100, 100);
                $droughtRiskPercent = min(($droughtRisk / 1e-8) * 100, 100);

                // Calculate Water Level Index (WLI)
                $waterLevelIndex = 50 + ($floodRiskPercent - $droughtRiskPercent);
                $waterLevelIndex = max(0, min($waterLevelIndex, 100));

                $res = [];
                $res['waterLevelIndex'] = $waterLevelIndex;
                $res['droughtRiskPercent'] = $droughtRiskPercent;
                $res['floodRiskPercent'] = $floodRiskPercent;
                $res['date'] = $response->json()['target_date'];

                $results[] = $res;
            }

            $predictionArray = [];
            $predictionArray['zone_id'] = $request->input('zone_id');
            $predictionArray['date'] = $startDate;
            $predictionArray['d1_risk'] = $results[0];
            $predictionArray['d2_risk'] = $results[1];
            $predictionArray['d3_risk'] = $results[2];
            $predictionArray['d4_risk'] = $results[3];
            $predictionArray['d5_risk'] = $results[4];

            $prediction = Prediction::create($predictionArray);
                        // Retourner la réponse de l'API de prédiction
            return response()->json([
                'success' => true,
                'prediction' => $response->json(),
                'res' => $results,
                'model' => $predictionArray,
                'predict' => $prediction
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
}
