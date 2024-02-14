<?php

namespace Database\Seeders;

use App\Models\MetricType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class MetricTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $hazards = ['DEGREE_OF_IMPACT', 'CLIMATE_VULNERABILITY', 'CLIMATE_RISK_THREATS'];

        foreach ($hazards as $hazard) {
            for ($i = 1; $i <= 3; $i++) {
                MetricType::create([
                    'name' => "Metric Type $i for $hazard,
                    'hazard' => $hazard
                ]);
            }
        }
    }
}
