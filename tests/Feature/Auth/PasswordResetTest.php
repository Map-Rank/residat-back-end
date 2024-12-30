<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;


    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->admin()->create();

        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

       

        $response->assertStatus(200);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        // Créez un utilisateur
        $user = User::factory()->admin()->create();

        // Envoyez une demande de réinitialisation du mot de passe
        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $token = $notification->token;

            // Authentifiez l'utilisateur avant d'accéder à la route de réinitialisation du mot de passe
            $this->actingAs($user);

            $response = $this->postJson("/api/reset-password", [
                'token' => $token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        // Créez un utilisateur
        $user = User::factory()->admin()->create();

        // Envoyez une demande de réinitialisation du mot de passe
        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $token = $notification->token;

            // Authentifiez l'utilisateur avant d'accéder à la route de réinitialisation du mot de passe
            $this->actingAs($user);

            $response = $this->postJson('/api/reset-password', [
                'token' => $token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_old_password_is_incorrect()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword')
        ]);

        $this->actingAs($user);

        $response = $this->putJson('/api/password/update', [
            'old_password' => 'wrongpassword', // Ancien mot de passe incorrect
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['old_password']);
        $response->assertJsonFragment([
            'old_password' => ['L\'ancien mot de passe est incorrect.']
        ]);
    }

    public function test_passwords_do_not_match()
    {
        $this->withExceptionHandling();
        
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword')
        ]);

        $this->actingAs($user);

        $response = $this->putJson('/api/password/update', [
            'old_password' => 'oldpassword', // Ancien mot de passe correct
            'password' => 'newpassword',
            'password_confirmation' => 'differentpassword', // Les mots de passe ne correspondent pas
        ]);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);
        $response->assertJsonFragment([
            'password' => ['The password field confirmation does not match.']
        ]);
    }

    public function test_password_update_successful()
    {
        $user = User::factory()->create([
            'email' => 'user@example.com',
            'password' => Hash::make('oldpassword')
        ]);

        $this->actingAs($user);

        $response = $this->putJson('/api/password/update', [
            'old_password' => 'oldpassword', // Ancien mot de passe correct
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword', // Les mots de passe correspondent
        ]);

        $response->assertStatus(200);
        $response->assertJson([
            'message' => 'Le mot de passe a été mis à jour avec succès.'
        ]);

        // Vérifier que le mot de passe a bien été mis à jour dans la base de données
        $this->assertTrue(Hash::check('newpassword', $user->fresh()->password));
    }
}
