<?php

namespace App\Http\Controllers\Api\V2;

use Carbon\Carbon;
use App\Models\Zone;
use App\Models\Prediction;
use App\Service\UtilService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Resources\PredictionResource;

class PredictionController extends Controller
{
    public function getPredictions(Request $request)
    {


        try {
            // Validation des paramètres
            $request->validate([
                'zone_id' => 'required|exists:zones,id',
                'date' => 'required|date_format:Y-m-d',
            ]);

            // Récupération des prédictions
            $predictions = Prediction::query()
                ->with('zone:id,name') // On inclut uniquement l'id et le nom de la zone
                ->where('zone_id', $request->zone_id)
                ->where('date', $request->date)
                ->first();

            if (!$predictions) {
                return response()->success([],  __('Aucune prédiction trouvée pour cette zone et cette date'), 200);
            }

            // Formater la réponse
            // $response = [
            //     'success' => true,
            //     'timestamp' => Carbon::now()->toDateTimeString(),
            //     'zone' => $predictions->zone->name,
            //     'date' => $predictions->date->format('Y-m-d'),
            //     'predictions' => [
            //         'day_1' => [
            //             'risk' => $predictions->d1_risk,
            //             'level' => $this->getRiskLevel($predictions->d1_risk)
            //         ],
            //         'day_2' => [
            //             'risk' => $predictions->d2_risk,
            //             'level' => $this->getRiskLevel($predictions->d2_risk)
            //         ],
            //         'day_3' => [
            //             'risk' => $predictions->d3_risk,
            //             'level' => $this->getRiskLevel($predictions->d3_risk)
            //         ],
            //         'day_4' => [
            //             'risk' => $predictions->d4_risk,
            //             'level' => $this->getRiskLevel($predictions->d4_risk)
            //         ],
            //         'day_5' => [
            //             'risk' => $predictions->d5_risk,
            //             'level' => $this->getRiskLevel($predictions->d5_risk)
            //         ]
            //     ]
            // ];

            return response()->success(new PredictionResource($predictions),  __('Prediction charged successfully'), 200);

        } catch (\Exception $e) {
            Log::error("Exception dans getPredictions: " . $e->getMessage());
            return response()->errors([],  __('Oups error'), 200);
        }
    }

    /**
     * Get risk level based on risk value
     */
    private function getRiskLevel($risk): string
    {
        if ($risk === null) return 'unknown';
        
        if ($risk <= 0.3) return 'low';
        if ($risk <= 0.6) return 'medium';
        return 'high';
    }

    public function predictionStore(Request $request){
         // Validation des paramètres
         $request->validate([
            'zone_id' => 'required|exists:zones,id',
            'date' => 'required|date_format:Y-m-d',
        ]);

        // $validated = $request->validated();

        $zone = Zone::query()->where('id', $request->zone_id)->first();

        // if($zone == null){
        //     // Response bad zone parameter
        // }

        // $latitude = 9.1125;
        // $longitude = 15.2306;

        $distToRiver = $zone->dist_to_river;
        $soil_type = $zone->soil_type;
        $longitude = $zone->longitude;
        $latitude = $zone->latitude;
        $hist_precipation = $zone->hist_precipitation; 
        $hist_temperature = $zone->hist_temperature; 
        $hist_std_rain = $zone->hist_std_rain;
        $hist_std_temp = $zone->hist_std_temp;
        $max_possible_sunshine_hrs = $zone->max_possible_sunshine_hrs;
        
        // $distToRiver = 1;
        // $soil_type = 'LOAMY';
        // $hist_precipation = 15; 
        // $hist_temperature = 30; 
        // $hist_std_rain = 50;
        // $hist_std_temp = 5;
        // $max_possible_sunshine_hrs = 7;

        $forcast = UtilService::getLocationForecast($longitude, $latitude);
        $risks = [];

        // return $forcast;
        if($forcast['success']){
            $data = $forcast['data'];
            $i = 0;

            foreach($data as $datum){
                $temp = ($datum['temperature']['average'] - $hist_temperature) / $hist_std_temp;
                $precipitation = ($datum['precipitation'] - $hist_precipation) / $hist_std_rain;
                $riverFactor = 1/$distToRiver;
                if($soil_type == 'SANDY'){
                    $soilIndex = 1;
                }
                else if($soil_type == 'LOAMY'){
                    $soilIndex = 2;
                }
                else if($soil_type == 'SILKY'){
                    $soilIndex = 2.5;
                }
                else if($soil_type == 'CLAY'){
                    $soilIndex = 3;
                }else {
                    $soilIndex = 1;
                }

                $evaporationRate = $temp * $soilIndex;

                $risk = $evaporationRate + $temp + $precipitation + $riverFactor;

                $risk_percentage = ( $risk * 100 / 2.5 );
                $risks[] = $risk_percentage;
            }
            $values = [];
            $i = 1;
            foreach($risks as $risk_percentage){
                $key = 'day'.$i.'_risk';
                $values[$key] = $risk_percentage;
                $i ++;
            }
            $values['date'] = $zone->date;
            $values['zone_id'] = $zone->id;
            Prediction::create($values);
        }else {
            return 1;
        }
    }


}
