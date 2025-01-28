<?php

namespace App\Http\Controllers\Api\V2;

use Carbon\Carbon;
use App\Models\Prediction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

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
                return response()->json([
                    'success' => false,
                    'message' => 'Aucune prédiction trouvée pour cette zone et cette date'
                ], 404);
            }

            // Formater la réponse
            $response = [
                'success' => true,
                'timestamp' => Carbon::now()->toDateTimeString(),
                'zone' => $predictions->zone->name,
                'date' => $predictions->date->format('Y-m-d'),
                'predictions' => [
                    'day_1' => [
                        'risk' => $predictions->d1_risk,
                        'level' => $this->getRiskLevel($predictions->d1_risk)
                    ],
                    'day_2' => [
                        'risk' => $predictions->d2_risk,
                        'level' => $this->getRiskLevel($predictions->d2_risk)
                    ],
                    'day_3' => [
                        'risk' => $predictions->d3_risk,
                        'level' => $this->getRiskLevel($predictions->d3_risk)
                    ],
                    'day_4' => [
                        'risk' => $predictions->d4_risk,
                        'level' => $this->getRiskLevel($predictions->d4_risk)
                    ],
                    'day_5' => [
                        'risk' => $predictions->d5_risk,
                        'level' => $this->getRiskLevel($predictions->d5_risk)
                    ]
                ]
            ];

            return response()->json($response);

        } catch (\Exception $e) {
            Log::error("Exception dans getPredictions: " . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des prédictions: ' . $e->getMessage()
            ], 500);
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
}
