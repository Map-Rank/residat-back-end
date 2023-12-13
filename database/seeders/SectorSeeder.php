<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sector::updateOrCreate(['id'=> 1],['name' => 'Centre']);
        Sector::updateOrCreate(['id'=> 2],['name' => 'Ouest']);
        Sector::updateOrCreate(['id'=> 3],['name' => 'Est']);
        Sector::updateOrCreate(['id'=> 4],['name' => 'Sud']);
        Sector::updateOrCreate(['id'=> 5],['name' => 'Nord']);
        Sector::updateOrCreate(['id'=> 6],['name' => 'Nord-Ouest']);
        Sector::updateOrCreate(['id'=> 7],['name' => 'Sud-Ouest']);
        Sector::updateOrCreate(['id'=> 8],['name' => 'Extreme-Nord']);
        Sector::updateOrCreate(['id'=> 9],['name' => 'Littoral']);
        Sector::updateOrCreate(['id'=> 10],['name' => 'Adamaoua']);
    }
}
