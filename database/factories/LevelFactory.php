<?php


namespace Database\Factories;

use App\Models\Level;
use Illuminate\Database\Eloquent\Factories\Factory;

class LevelFactory extends Factory
{
    protected $model = Level::class;

    public function definition()
    {
        return [
            'name' => $this->faker->word,
            // Ajoutez d'autres champs et valeurs au besoin
        ];
    }
}