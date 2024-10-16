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
            'past_days' => 'required|integer|min:1',
        ]);

        // Récupérer les paramètres depuis la requête
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $past_days = $request->input('past_days', 4); // par défaut à 4 si non fourni

        // Construction de l'URL de l'API avec les paramètres
        $url = "https://archive-api.open-meteo.com/v1/archive";
        $response = Http::get($url, [
            'latitude' => $latitude,
            'longitude' => $longitude,
            'hourly' => 'temperature_2m,rain,soil_temperature_0_to_7cm,soil_moisture_0_to_7cm,precipitation',
            'past_days' => $past_days,
        ]);

        // Vérifier si la requête a réussi
        if ($response->successful()) {
            return response()->success($response, __('Data fetch successfully'), 200);
        }

        return response()->success($response, __('Unable to fetch weather data'), 400);
    }
}
