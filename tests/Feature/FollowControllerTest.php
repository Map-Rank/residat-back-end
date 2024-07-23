<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;

class FollowControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test follow user successfully.
     *
     * @return void
     */
    public function test_follow_user_successfully()
    {
        // Créer le premier utilisateur (propriétaire de l'événement)
        $user1 = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $user2 = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Agis en tant que user1
        $this->actingAs($user1, 'sanctum');

        // Envoie une requête POST pour suivre user2
        $response = $this->postJson('/api/follow/' . $user2->id);

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Successfully followed user.']);

        // Vérifie la relation dans la base de données
        $this->assertTrue($user1->following()->where('followed_id', $user2->id)->exists());
    }

    /**
     * Test follow user who is already followed.
     *
     * @return void
     */
    public function test_follow_user_already_followed()
    {

        // Créer le premier utilisateur (propriétaire de l'événement)
        $user1 = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $user2 = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Agis en tant que user1
        $this->actingAs($user1, 'sanctum');

        // Suivre user2
        $user1->following()->attach($user2->id);

        // Envoie une requête POST pour suivre user2 à nouveau
        $response = $this->postJson('/api/follow/' . $user2->id);

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJson(['message' => 'You already follow this user.']);
    }

    /**
     * Test unfollow user successfully.
     *
     * @return void
     */
    public function test_unfollow_user_successfully()
    {
        // Créer le premier utilisateur (propriétaire de l'événement)
        $user1 = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $user2 = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Agis en tant que user1
        $this->actingAs($user1, 'sanctum');

        // Suivre user2
        $user1->following()->attach($user2->id);

        // Envoie une requête POST pour ne plus suivre user2
        $response = $this->postJson('/api/unfollow/' . $user2->id);

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJson(['message' => 'You Successfully unfollowed user.']);

        // Vérifie la relation dans la base de données
        $this->assertFalse($user1->following()->where('followed_id', $user2->id)->exists());
    }

    /**
     * Test unfollow user who is not followed.
     *
     * @return void
     */
    public function test_unfollow_user_not_followed()
    {
        // Créer le premier utilisateur (propriétaire de l'événement)
        $user1 = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $user2 = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Agis en tant que user1
        $this->actingAs($user1, 'sanctum');

        // Envoie une requête POST pour ne plus suivre user2
        $response = $this->postJson('/api/unfollow/' . $user2->id);

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJson(['message' => 'You don\'t follow this user.']);
    }

    /**
     * Test retrieving followers of a specific user.
     *
     * @return void
     */
    public function test_retrieving_followers_of_user()
    {
        // Créer le premier utilisateur (propriétaire de l'événement)
        $user1 = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $user2 = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Agis en tant que user1
        $this->actingAs($user1, 'sanctum');

        // Suivre user1
        $user2->following()->attach($user1->id);

        // Envoie une requête GET pour obtenir les followers de user1
        $response = $this->getJson('/api/followers/' . $user1->id);

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $user2->id]);
    }

    /**
     * Test retrieving following of a specific user.
     *
     * @return void
     */
    public function test_retrieving_following_of_user()
    {
        // Créer le premier utilisateur (propriétaire de l'événement)
        $user1 = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $user2 = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Agis en tant que user2
        $this->actingAs($user2, 'sanctum');

        // Suivre user2
        $user1->following()->attach($user2->id);

        // Envoie une requête GET pour obtenir les suivis de user1
        $response = $this->getJson('/api/following/' . $user1->id);

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJsonFragment(['id' => $user2->id]);
    }
}