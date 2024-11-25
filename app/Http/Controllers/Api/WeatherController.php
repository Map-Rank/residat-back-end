<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;

/**
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
        // Chemin du fichier à traiter
        $filePath = storage_path('app/data.csv');

        // Exécuter le script Python
        $command = escapeshellcmd("python3 " . base_path('scripts/data_processing.py') . " " . $filePath);
        $output = shell_exec($command);

        // Convertir la sortie JSON en tableau PHP
        $data = json_decode($output, true);

        return response()->json($data);
    }
}
