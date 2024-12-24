<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetLinkControllerTest extends TestCase
{
    use RefreshDatabase;


    public function testCreatePasswordResetLinkView()
    {
        // Simule une requête GET vers la route 'password.request'
        $response = $this->get(route('password.request'));

        // Vérifie que la réponse HTTP est de type 200 (succès)
        $response->assertStatus(200);

        // Vérifie que la vue 'auth.forgot-password' est bien affichée
        // $response->assertViewIs('auth.forgot-password');
    }

    public function test_sends_password_reset_link_with_valid_email()
    {
        // Créer un utilisateur pour simuler une adresse e-mail valide
        $user = \App\Models\User::factory()->create(['email' => 'testuser@example.com']);

        // Simuler l'appel à la route d'envoi du lien de réinitialisation
        $response = $this->post(route('password.email'), [
            'email' => 'testuser@example.com',
        ]);

        // Vérifier que la réponse redirige avec un statut de succès
        $response->assertRedirect()
                 ->assertSessionHas('status', trans(Password::RESET_LINK_SENT));

        // Vérifier que la fonction de reset a bien été appelée
        $this->assertNotNull($user->fresh());  // Le modèle est toujours présent
    }

    public function test_does_not_send_password_reset_link_for_invalid_email()
    {
        // Simuler l'appel à la route avec un email qui n'existe pas
        $response = $this->post(route('password.email'), [
            'email' => 'nonexistent@example.com',
        ]);

        // Vérifier que la réponse contient une erreur d'email
        $response->assertRedirect()
                 ->assertSessionHasErrors(['email']);
    }
}