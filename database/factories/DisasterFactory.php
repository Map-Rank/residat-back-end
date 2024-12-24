<?php

namespace Database\Factories;

use App\Models\Disaster;
use App\Models\Zone;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Carbon\Carbon;

class DisasterFactory extends Factory
{
    protected $model = Disaster::class;

    public function definition()
    {
        // Générer d'abord la date de début pour l'utiliser dans la date de fin
        $startDate = $this->faker->dateTimeThisDecade();
        
        return [
            'description' => fake()->realText(100),
            'locality' => fake()->city(),
            'latitude' => fake()->randomFloat(6, -90, 90),
            'longitude' => fake()->randomFloat(6, -180, 180),
            'image' => 'https://picsum.photos/640/480', // Utilisation de Lorem Picsum à la place
            'zone_id' => Zone::factory(), 
            'level' => fake()->numberBetween(1, 5),
            'type' => fake()->randomElement([
                'FLOOD',
                'DROUGHT',
                // 'EARTHQUAKE',
                // 'HURRICANE',
                // 'WILDFIRE'
            ]),
            'start_period' => Carbon::parse($startDate)->format('Y-m-d'),
            'end_period' => Carbon::parse($startDate)
                ->addDays(fake()->numberBetween(1, 30))
                ->format('Y-m-d'),
        ];
    }

    // Méthode pour créer des désastres en cours
    public function ongoing()
    {
        return $this->state(function (array $attributes) {
            $startDate = Carbon::now()->subDays($this->faker->numberBetween(1, 10));
            
            return [
                'start_period' => $startDate->format('Y-m-d'),
                'end_period' => null, // Désastre en cours
            ];
        });
    }
}
