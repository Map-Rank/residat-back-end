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
        Sector::updateOrCreate(['id'=> 1],['name' => 'Agriculture']);
        Sector::updateOrCreate(['id'=> 2],['name' => 'Social']);
        Sector::updateOrCreate(['id'=> 3],['name' => 'Economics']);
        Sector::updateOrCreate(['id'=> 4],['name' => 'Education']);
        Sector::updateOrCreate(['id'=> 5],['name' => 'Socials']);
        Sector::updateOrCreate(['id'=> 6],['name' => 'Climate event']);
    }
}
