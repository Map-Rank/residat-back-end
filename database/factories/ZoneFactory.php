<?php

namespace Database\Factories;

use App\Models\Zone;
use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class ZoneFactory extends Factory
{
    protected $model = Zone::class;

    public function definition()
    {
        $parentZone = Zone::inRandomOrder()->first();

        return [
            'name' => $this->faker->word,
            'parent_id' => $parentZone ? $parentZone->id : null,
            'level_id' => Level::factory(), // Utilisation de la factory pour créer un niveau
        ];
    }
}