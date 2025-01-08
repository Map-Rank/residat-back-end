<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

/**
 * @codeCoverageIgnore
 * @group Module WeatherPrediction
 */
class WeatherController extends Controller
{
    /**
     * Get Weather Data
     *
     */
    public function getWeatherData(Request $request)
    {
        // Vérification des paramètres nécessaires
        $validated = $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'past_days' => 'nullable|integer|min:1',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date',
        ]);

        // Récupérer les paramètres depuis la requête
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $past_days = $request->input('past_days', 4); // Par défaut 4 jours
        $start_date = $request->input('start_date');
        $end_date = $request->input('end_date');

        // Construction des paramètres `hourly`
        $hourly = [
            'temperature_2m',
            'rain',
            'soil_temperature_0_to_7cm',
            'soil_moisture_0_to_7cm',
            'precipitation'
        ];

        // Construction de l'URL de l'API avec les paramètres
        $url = "https://archive-api.open-meteo.com/v1/archive";
        $queryParams = [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => implode(',', $hourly),
        ];

        // Ajout des dates si elles sont fournies
        if ($start_date) {
            $queryParams['start_date'] = $start_date;
        }
        if ($end_date) {
            $queryParams['end_date'] = $end_date;
        }

        if($past_days) {
            $queryParams['past_days'] = $past_days;
        }

        // Exécution de la requête GET
        $response = Http::get($url, $queryParams);

        // Vérification du succès de la requête
        if ($response->successful()) {
            return response()->json($response->json());return response()->success($response, __('Data fetch successfully'), 200);
        }

        return response()->success($response, __('Unable to fetch weather data'), 400);
    }

    public function processData(Request $request)
    {
        // Chemin du fichier CSV à traiter
        $inputPath = public_path('storage/weather_data.csv');

        // Chemin pour enregistrer le fichier nettoyé
        $outputPath = storage_path('app/cleaned_weather_data.csv');

        // Commande pour exécuter le script Python
        $pythonScript = base_path('scripts/clean_dataset.py');
        $command = escapeshellcmd("python3 $pythonScript $inputPath $outputPath");

        // Exécuter le script et capturer la sortie
        $output = shell_exec($command);

        // Vérifier si le fichier nettoyé existe
        if (!file_exists($outputPath)) {
            return response()->json(['error' => 'Le traitement des données a échoué.'], 500);
        }

        // Charger le fichier nettoyé pour le retourner dans la réponse
        $cleanedData = array_map('str_getcsv', file($outputPath));
        return response()->json(['message' => 'Données traitées avec succès.', 'data' => $cleanedData]);
    }
}
