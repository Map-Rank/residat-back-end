<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]); 

        $response->assertRedirect(RouteServiceProvider::HOME);
        $this->assertAuthenticated();
        $response = $this->get(RouteServiceProvider::HOME);
        $response->assertStatus(200); // Vous pouvez ajuster ceci selon le comportement attendu
        
    }

    // public function test_users_can_not_authenticate_with_invalid_password(): void
    // {
    //     $user = User::factory()->create();

    //     $this->post('/login', [
    //         'email' => $user->email,
    //         'password' => 'wrong-password',
    //     ]);

    //     $this->assertGuest();
    // }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect('/');
    }

    /**
     * Test verifyToken with valid token.
     *
     * @return void
     */
    public function test_verify_token_with_valid_token()
    {
        // Crée un utilisateur
        $user = User::factory()->create();

        // Agis en tant que cet utilisateur
        $this->actingAs($user, 'sanctum');

        // Envoie une requête POST à la route /verify-token
        $response = $this->postJson('api/verify-token');

        // Vérifie la réponse
        $response->assertStatus(200)
                 ->assertJson(['message' => 'Token is valid']);
    }

    /**
     * Test verifyToken with invalid token.
     *
     * @return void
     */
    public function test_verify_token_with_invalid_token()
    {
        // Désactive le middleware pour ce test spécifique
        $this->withoutMiddleware();

        // Envoie une requête POST à la route /verify-token sans authentification
        $response = $this->postJson('api/verify-token');

        // Vérifie la réponse
        $response->assertStatus(401)
                 ->assertJson(['message' => 'Token is not valid']);
    }

}
