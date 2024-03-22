<?php

namespace Database\Seeders;

use App\Models\Report;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ReportsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = ['DROUGHT', 'FLOOD', 'WATER_STRESS'];

        foreach ($types as $type) {
            for ($i = 1; $i <= 3; $i++) {
                $code = "CODE_$type" . "_$i";
                $description = "Description for $type report $i";
                $image = "image_$type" . "_$i.jpg";

                Report::create([
                    'code' => $code,
                    'user_id' => 1,
                    'zone_id' => 1,
                    'description' => $description,
                    'type' => $type,
                    'image' => $image,
                    'start_date' => now(),
                    'end_date' => now()->addDays(7),
                ]);
            }
        }
    }
}
