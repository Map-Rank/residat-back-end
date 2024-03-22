<?php

namespace Database\Factories;

use App\Models\TypeInteraction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeInteraction>
 */
class TypeInteractionFactory extends Factory
{

    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = TypeInteraction::class;
    
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['created', 'like', 'comment', 'share'];
        $ids = [1, 2, 3, 4];

        return [
            'name' => $types[array_rand($types)],
            'id' => $ids[array_rand($ids)],
        ];
    }
}
