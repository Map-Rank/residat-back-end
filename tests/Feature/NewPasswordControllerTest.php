<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Tests\TestCase;

class NewPasswordControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the password reset view can be displayed.
     *
     * @return void
     */
    public function test_reset_password_view_can_be_displayed()
    {
        $response = $this->get(route('password.reset', ['token' => 'dummy-token']));

        $response->assertStatus(200);
        $response->assertViewIs('auth.reset-password');
    }

    /**
     * Test the new password can be set.
     *
     * @return void
     */
    public function test_user_can_reset_password()
    {
        Event::fake();

        $response = $this->post(route('password.store'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        // $response->assertRedirect(route('login'));
        $response->assertSessionHas('status', Password::PASSWORD_RESET);

        $this->assertTrue(Hash::check('new-password', $user->fresh()->password));
    }

    // /**
    //  * Test reset password fails with invalid token.
    //  *
    //  * @return void
    //  */
    // public function test_reset_password_fails_with_invalid_token()
    // {
    //     $user = User::first();

    //     if (!$user) {
    //         $user = User::factory()->admin()->create();
    //     }

    //     $this->actingAs($user);

    //     $token = Password::createToken($user);

       

    //     $response = $this->put(route('password.update'), [
    //         'token' => 1365868,
    //         'email' => $user->email,
    //         'current_password' => 'password',
    //         'password' => 'new-password',
    //         'password_confirmation' => 'new-password',
    //     ]);
    //      dd($response);

    //     $response->assertRedirect();
    //     $response->assertSessionHasErrors(['email']);
    // }

    public function test_password_reset_fails_and_returns_error()
    {
        // Créer un utilisateur factice
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('oldpassword'),
        ]);

        $this->actingAs($user);

        // Simuler une requête de réinitialisation de mot de passe
        $response = $this->post(route('password.store'), [
            'email' => 'test@example.com',
            'current_password' => 'oldpassword',
            'password' => 'newpassword',
            'password_confirmation' => 'newpassword',
            'token' => 321658,
        ]);

        // Vérifier que la réponse est une redirection vers la page précédente
        $response->assertRedirect();

        // Vérifier que l'erreur est présente avec la clé 'error'
        $response->assertSessionHas('error', 'This password reset token is invalid.');

        // Vérifier que l'ancien email est retourné avec la redirection
        $response->assertSessionHasInput(['email' => 'test@example.com']);
    }
    
    /**
     * Test reset password fails with invalid email.
     *
     * @return void
     */
    public function test_reset_password_fails_with_invalid_email()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        $token = Password::createToken($user);

        $response = $this->put(route('password.update'), [
            'token' => $token,
            'email' => 'invalid-email@example.com',
            'current_password' => 'password!',
            'password' => 'new-password',
            'password_confirmation' => 'new-password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['email']);
    }
}
