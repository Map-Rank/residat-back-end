<?php

namespace Database\Seeders;

use App\Models\Level;
use App\Models\Zone;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {


        $levels = [
            [
                'name'=>'Country',
            ],
            [
                'name'=>'Region',
            ],
            [
                'name'=>'Division',
            ],
            [
                'name'=>'SubDivision',
            ],

            ];

            foreach($levels as $level){
                Level::query()->updateOrCreate($level);
            }

    }
}
