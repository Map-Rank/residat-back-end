<?php

namespace Database\Factories;

use App\Models\Vector;
use Illuminate\Database\Eloquent\Factories\Factory;

class VectorFactory extends Factory
{
    protected $model = Vector::class;

    public function definition()
    {
        return [
            'path' => $this->faker->imageUrl(),
            'model_id' => $this->faker->randomNumber(),
            'category' => $this->faker->word(),
            'type' => $this->faker->mimeType(),
            'model_type' => 'App\Models\Report',
        ];
    }
}
