<?php

namespace App\Jobs;

use App\Models\WeatherPrediction;
use App\Models\Zone;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherFetchJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $zoneId;
    /**
     * Create a new job instance.
     */
    public function __construct(int $zone_id)
    {
        $this->zoneId = $zone_id;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $zone = Zone::find($this->zoneId);

        if($zone == null)
        {
            return ;
        }

        // Construction des paramètres `hourly`
        $daily = [
            'temperature_2m',
            'relative_humidity_2m',
            'rain',
            'soil_temperature_6cm',
            'soil_moisture_3_to_9cm'
        ];

        // Construction de l'URL de l'API avec les paramètres
        $url = "https://api.open-meteo.com/v1/forecast";
        $queryParams = [
            'latitude' => $zone->latitude,
            'longitude' => $zone->longitude,
            'hourly' => implode(',', $daily),
            'past_days' => 0,
            'forecast_days'=> 1,
        ];

        // Exécution de la requête GET
        $response = Http::get($url, $queryParams);

        // Vérification du succès de la requête
        if ($response->successful()) {
            $object = ($response->json());

            Log::info(sprintf('%s: The hourly : %s', __METHOD__, json_encode($object['hourly'])));

            $prediction = WeatherPrediction::query()->where('zone_id', $zone->id)->first();

            if($prediction == null )
            {
                $filePath = storage_path('app/public/weather/weather_'.time().'.csv');
                $prediction = WeatherPrediction::create([
                    'zone_id' => $zone->id,
                    'date' => now(),
                    'path' => $filePath,
                    'created_at' => now(),
                ]);
                $fileHeader = ['Date', 'Location', 'Precipitation', 'Elevation', 'Temperature', 'Humidity',
                    'Soil Temperature', 'Soil Moisture'];

                file_put_contents($filePath, implode(',', $fileHeader) . PHP_EOL);
            }

            $filePath = $prediction->path;

            $file = fopen($filePath, 'a');

            $rainData = $object['hourly']['rain'];
            $temperature_2mData = $object['hourly']['temperature_2m'];
            $relative_humidity_2mData = $object['hourly']['relative_humidity_2m'];
            $soil_temperature_6cm = $object['hourly']['soil_temperature_6cm'];
            $soil_moisture_3_to_9cm = $object['hourly']['soil_moisture_3_to_9cm'];

            $rainAvg = array_sum($rainData) / count($rainData);
            $temperatureAvg = array_sum($temperature_2mData) / count($temperature_2mData);
            $humidityAvg = array_sum($relative_humidity_2mData) / count($relative_humidity_2mData);
            $soilTempAvg = array_sum($soil_temperature_6cm) / count($soil_temperature_6cm);
            $moistureAvg = array_sum($soil_moisture_3_to_9cm) / count($soil_moisture_3_to_9cm);

            $row = [
                Carbon::now(),
                $zone->id,
                $rainAvg,
                $object['elevation'],
                $temperatureAvg,
                $humidityAvg,
                $soilTempAvg,
                $moistureAvg
            ];


            // foreach ($errors as $row) {
            fputcsv($file, $row);
            // }
            fclose($file);


        }else {
            Log::info(sprintf('%s: Unable to get data for zone : %s', __METHOD__, $zone->id));
        }


    }
}
