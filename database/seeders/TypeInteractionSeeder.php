<?php

namespace Database\Seeders;

use App\Models\TypeInteraction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TypeInteractionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeInteraction::updateOrCreate(['id'=> 1],['name' => 'created']);
        TypeInteraction::updateOrCreate(['id'=> 2],['name' => 'like']);
        TypeInteraction::updateOrCreate(['id'=> 3],['name' => 'comment']);
        TypeInteraction::updateOrCreate(['id'=> 4],['name' => 'share']);
    }
}
