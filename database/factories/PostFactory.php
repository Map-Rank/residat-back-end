<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'content' => $this->faker->sentence(),
            'published_at' => now(),
            'zone_id' => function () {
                // Retourne un ID de zone existant ou crée un nouveau
                return \App\Models\Zone::inRandomOrder()->first()->id ?? \App\Models\Zone::factory()->create()->id;
            },
            'user_id' => function () {
                // Retourne un ID d'utilisateur existant ou crée un nouveau
                return \App\Models\User::inRandomOrder()->first()->id ?? \App\Models\User::factory()->create()->id;
            },
            'topic_id' => function () {
                // Retourne un ID de topic existant ou crée un nouveau
                return \App\Models\Topic::inRandomOrder()->first()->id ?? \App\Models\Topic::factory()->create()->id;
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
