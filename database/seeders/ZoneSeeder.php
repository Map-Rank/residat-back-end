<?php

namespace Database\Seeders;

use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ZoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        $cameroun = Zone::query()->create([
            'name' => 'Cameroun',
            'level_id' => 1,
        ]);

        $regions = [
            [
                'name'=>'ADAMAWA',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'CENTER',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'EAST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,
            ],
            [
                'name'=>'LITTORAL',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ],
            [
                'name'=>'FAR NORTH',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ],
            [
                'name'=>'NORTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ],
            [
                'name'=>'NORTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ],
            [
                'name'=>'SOUTH WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ],
            [
                'name'=>'SOUTH REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ],
            [
                'name'=>'WEST REGION',
                'level_id' => 2,
                'parent_id' => $cameroun->id,

            ]
            ];

            foreach($regions as $region){
                Zone::query()->updateOrCreate($region);
            }

    }
}
