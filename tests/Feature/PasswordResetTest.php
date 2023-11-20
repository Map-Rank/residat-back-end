<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use App\Models\Zone;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_can_be_requested(): void
    {
        // $this->withoutExceptionHandling();

        Notification::fake();

        $user = User::factory()->create($this->dataLogin());

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create($this->dataLogin());

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPassword::class, function (object $notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'users@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }

    private function dataRegister()
    {
        $zone = Zone::factory()->create();
        
        return [
            'first_name' => 'users',
            'last_name' => 'last name',
            'phone' => '237698803159',
            'date_of_birth' => '1996-03-11',
            'email' => 'users@user.com',
            'password' => bcrypt('password'),
            'gender' => 'male',
            'zone_id' => $zone->id,
            'active' => 1,
            'verified' => 1,
        ];
    }
}
