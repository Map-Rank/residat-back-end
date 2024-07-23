<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // public function test_reset_password_link_screen_can_be_rendered(): void
    // {
    //     $response = $this->get('/forgot-password');

    //     $response->assertStatus(200);
    // }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $response = $this->postJson('/api/forgot-password', ['email' => $user->email]);

       

        $response->assertStatus(200);
        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        // Créez un utilisateur
        $user = User::factory()->create();

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
        $user = User::factory()->create();

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
}
