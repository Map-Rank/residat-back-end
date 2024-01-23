<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Interaction;
use Laravel\Sanctum\Sanctum;
use App\Models\TypeInteraction;
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
                // Utilisez la méthode pour obtenir un ID de subdivision aléatoire
                return $this->getRandomSubdivisionId();
            },
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the post was created by the currently authenticated user.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function creator(): PostFactory
    {
        $user = User::first();
        
        return $this->afterCreating(function (Post $post) use ($user) {
            $typeInteraction = TypeInteraction::where('id', 1)->first();
            $interaction = new Interaction([
                'type_interaction_id' => $typeInteraction->id,
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

            $post->interactions()->save($interaction);
        });
    }

    /** 
     * Get a random subdivision ID.
     *
     * @return int
     */
    private function getRandomSubdivisionId(): int
    {
        $subdivision = Zone::where('level_id', Level::query()->latest()->first()->id)->inRandomOrder()->first();
        return $subdivision->id;
    }

}
