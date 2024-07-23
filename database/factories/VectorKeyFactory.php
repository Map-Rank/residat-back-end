<?php

namespace Database\Factories;

use App\Models\VectorKey;
use Illuminate\Database\Eloquent\Factories\Factory;

class VectorKeyFactory extends Factory
{
    protected $model = VectorKey::class;

    public function definition()
    {
        return [
            'value' => $this->faker->word(),
            'type' => $this->faker->word(),
            'name' => $this->faker->word(),
            'vector_id' => \App\Models\Vector::factory(),
        ];
    }
}
