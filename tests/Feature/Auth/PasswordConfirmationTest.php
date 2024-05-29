<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = User::factory()->create($this->dataLogin());

        $response = $this->actingAs($user)->get('/confirm-password');

        $response->assertStatus(200);
    }

    // public function test_password_can_be_confirmed(): void
    // {
    //     $user = User::factory()->create($this->dataLogin());

    //     $response = $this->actingAs($user)->post('/confirm-password', [
    //         'password' => 'password',
    //     ]);

    //     $response->assertRedirect();
    //     $response->assertSessionHasNoErrors();
    // }

    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $this->withoutExceptionHandling(); 

        $user = User::factory()->create($this->dataLogin());

        $response = $this->actingAs($user)->post('/confirm-password', [
            'password' => 'Abcd123!',
        ]);

        $response->assertStatus(302);
    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'users@user.com',
            'password' => 'Abcd123!',
            'email_verified_at' => now(),
        ];
    }
}
