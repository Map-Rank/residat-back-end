<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Notifications\CustomVerificationNotification;

class EmailVerificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_success_when_email_is_already_verified()
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $signedUrl = URL::signedRoute('verification.verify.custum', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $response = $this->getJson($signedUrl, ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'verified' => false,
                    'link_verification' => false,
                    'already_verified' => true,
                ],
                'message' => 'Email already verified',
            ]);
    }

    /** @test */
    public function it_verifies_email_if_not_already_verified()
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        Event::fake();

        $signedUrl = URL::signedRoute('verification.verify.custum', [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]);

        $response = $this->getJson($signedUrl, ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'verified' => true,
                    'link_verification' => false,
                    'already_verified' => false,
                ],
                'message' => 'Email has been verified',
            ]);

        $this->assertNotNull($user->fresh()->email_verified_at);

        Event::assertDispatched(Verified::class);
    }

    /** @test */
    public function it_returns_success_when_resending_verification_email_if_already_verified()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->actingAs($user);

        $response = $this->postJson(route('verification.resend.custum'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'verified' => false,
                    'link_verification' => false,
                    'already_verified' => true,
                ],
                'message' => 'Email already verified',
            ]);
    }

    /** @test */
    public function it_resends_verification_email_if_not_already_verified()
    {
        $user = User::factory()->unverified()->create();

        $this->actingAs($user);

        Notification::fake();

        $response = $this->postJson(route('verification.resend.custum'));

        $response->assertStatus(200)
            ->assertJson([
                'status' => true,
                'data' => [
                    'verified' => false,
                    'link_verification' => true,
                    'already_verified' => false,
                ],
                'message' => 'New email verification link sent successfully.',
            ]);

            Notification::assertSentTo($user, CustomVerificationNotification::class);
    }
}
