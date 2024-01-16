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
            'name' => $this->faker->word(),
            'parent_id' => null,
            'level_id' => Level::factory(),
        ];
    }

    /**
     * Configure la factory pour utiliser la dernière zone existante avec level_id = 4.
     *
     * @return $this
     */
    public function existingWithLevelFour()
    {
        return $this->state(function (array $attributes) {
            $latestZone = Zone::where('level_id', 4)->latest()->first();
            return [
                'parent_id' => $latestZone ? $latestZone->id : null,
            ];
        });
    }
}