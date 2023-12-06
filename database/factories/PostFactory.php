<?php

namespace Database\Factories;

use App\Models\Post;
use App\Models\User;
use App\Models\Interaction;
use Laravel\Sanctum\Sanctum;
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
        $user = Sanctum::actingAs(
            User::factory()->create($this->dataLogin())
        );
        
        return $this->afterCreating(function (Post $post) use ($user) {
            // Créez une interaction de type 'creator' pour l'utilisateur actuellement authentifié
            $interaction = new Interaction([
                'type_interaction_id' => 3,
                'user_id' => $user->id,
                'post_id' => $post->id,
            ]);

            $post->interactions()->save($interaction);
        });
    }

    // public function setUp(): void
    // {
    //     parent::setUp();

    //     $this->seed();
    //     Sanctum::actingAs(
    //         User::factory()->create($this->dataLogin())
    //     );
    // }

    private static function dataLogin()
    {
        return [
            'email' => 'simpleusers@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }

}
