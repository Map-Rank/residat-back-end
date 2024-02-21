<?php

namespace Database\Seeders;

use App\Models\Zone;
use App\Models\Report;
use App\Models\Vector;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class VectorsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['MAP', 'WATER_STRESS', 'DROUGHT', 'FLOOD'];
        $types = ['IMAGE', 'SVG'];

        foreach ($categories as $category) {
            foreach ($types as $type) {
                for ($i = 1; $i <= 3; $i++) {
                    $modelId = ($i % 2 == 0) ? Zone::inRandomOrder()->first()->id : Report::inRandomOrder()->first()->id;
                    $modelType = ($modelId instanceof Zone) ? 'App\Models\Zone' : 'App\Models\Report';

                    Vector::create([
                        'path' => "example_path_$i.$type",
                        'model_id' => $modelId,
                        'category' => $category,
                        'type' => $type,
                        'model_type' => $modelType,
                    ]);
                }
            }
        }
    }
}
