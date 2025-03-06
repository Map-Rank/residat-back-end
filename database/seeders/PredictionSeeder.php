<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Zone;
use App\Models\Prediction;
use Illuminate\Database\Seeder;

class PredictionSeeder extends Seeder
{
    /**
     * Les zones de l'extrême nord
     */
    private array $extremeNorthZones = [
        'Gobo',
        'Yagoua',
        'Wina',
        'Kai-kai',
        'Maga',
        'Gueme',
        'Kalfou',
        'Kar-hay',
        'Datcheka'
    ];

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Récupérer les IDs des zones de l'extrême nord
        $zoneIds = Zone::whereIn('name', $this->extremeNorthZones)
                      ->pluck('id');

        // Générer les dates de janvier 2025
        $startDate = Carbon::create(2025, 1, 1);
        $endDate = Carbon::create(2025, 1, 31);
        $dates = [];
        
        for($date = $startDate; $date->lte($endDate); $date->addDay()) {
            $dates[] = $date->format('Y-m-d');
        }

        // Pour chaque zone et chaque date, créer une prédiction
        foreach ($zoneIds as $zoneId) {
            foreach ($dates as $date) {
                // Générer les valeurs de risque aléatoires mais réalistes
                $baseRisk = $this->generateBaseRisk();
                
                Prediction::create([
                    'zone_id' => $zoneId,
                    'date' => $date,
                    'd1_risk' => $this->adjustRisk($baseRisk),
                    'd2_risk' => $this->adjustRisk($baseRisk),
                    'd3_risk' => $this->adjustRisk($baseRisk),
                    'd4_risk' => $this->adjustRisk($baseRisk),
                    'd5_risk' => $this->adjustRisk($baseRisk)
                ]);
            }
        }
    }

    /**
     * Générer un risque de base aléatoire
     */
    private function generateBaseRisk(): float
    {
        return round(rand(10, 90) / 100, 2);
    }

    /**
     * Ajuster le risque avec une légère variation
     */
    private function adjustRisk(float $baseRisk): float
    {
        $variation = rand(-15, 15) / 100;
        $risk = $baseRisk + $variation;
        
        // S'assurer que le risque reste entre 0 et 1
        return round(max(0, min(1, $risk)), 2);
    }
}