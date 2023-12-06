<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\TypeInteraction>
 */
class TypeInteractionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $types = ['created', 'like', 'comment', 'share'];

        // Mélanger le tableau aléatoirement
        shuffle($types);

        return [
            'name' => function () use ($types) {
                // Utiliser une closure pour garantir l'ordre fixe dans le tableau mélangé
                static $index = 0;
                return $types[$index++];
            },
        ];
    }
}
