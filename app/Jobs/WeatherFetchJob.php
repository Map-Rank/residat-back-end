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

/**
 * @codeCoverageIgnore
 */
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
        $url = "https://api.open-meteo.com/v1/archive";
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


    function groupDailyWeatherData($hourlyData) {
        // Extract the arrays from the input
        $times = $hourlyData["time"] ?? [];
        $temperaturesMax = $hourlyData["temperature_2m_max"] ?? [];
        $temperaturesMin = $hourlyData["temperature_2m_min"] ?? [];
        $precipitationSums = $hourlyData["precipitation_sum"] ?? [];
        $winSpeedMaxs = $hourlyData["wind_speed_10m_max"] ?? [];
        
        $mergedData = [];
        $count = min(count($times), count($humidities), count($soilMoistures));
        
        // Merge the arrays into the desired structure
        for ($i = 0; $i < $count; $i++) {
            $mergedData[] = [
                "time" => $times[$i],
                "temperature_2m_max" => $temperaturesMax[$i],
                "temperature_2m_min" => $temperaturesMin[$i],
                "precipitation_sum" => $precipitationSums[$i],
                "wind_speed_10m_max" => $winSpeedMaxs[$i]
            ];
        }

        return $mergedData;
    }

    // Get the fetched hourly data and yeild that in the an average daily data
    function groupAndAverageDailyData($hourlyData) {
        // Extract the arrays from the input
        $times = $hourlyData["time"] ?? [];
        $humidities = $hourlyData["relative_humidity_2m"] ?? [];
        $soilMoistures = $hourlyData["soil_moisture_0_to_7cm"] ?? [];
        
        $dailyData = [];

        // Iterate through the time array
        foreach ($times as $index => $timestamp) {
            $date = substr($timestamp, 0, 10); // Extract the date part (YYYY-MM-DD)
            
            if (!isset($dailyData[$date])) {
                $dailyData[$date] = [
                    "humidity_sum" => 0,
                    "moisture_sum" => 0,
                    "count" => 0
                ];
            }

            $dailyData[$date]["humidity_sum"] += $humidities[$index] ?? 0;
            $dailyData[$date]["moisture_sum"] += $soilMoistures[$index] ?? 0;
            $dailyData[$date]["count"]++;
        }

        // Calculate averages for each day
        $averagedData = [];
        foreach ($dailyData as $date => $data) {
            $averagedData[] = [
                "date" => $date,
                "average_humidity" => $data["humidity_sum"] / $data["count"],
                "average_soil_moisture" => $data["moisture_sum"] / $data["count"]
            ];
        }

        return $averagedData;
    }
}
