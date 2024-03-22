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
        $hazardData = [
            'DEGREE_OF_IMPACT' => ['Degree of impact'],
            'CLIMATE_VULNERABILITY' => ['Health', 'Agriculture', 'Infrastructure', 'Business', 'Social'],
            'CLIMATE_RISK_THREATS' => ['Food security', 'Water stress', 'Epidemics', 'Migration']
        ];

        foreach ($hazardData as $hazard => $names) {
            foreach ($names as $name) {
                MetricType::create([
                    'name' => $name,
                    'hazard' => $hazard
                ]);
            }
        }
    }
}
