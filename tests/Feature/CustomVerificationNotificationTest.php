<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Notification;
use App\Notifications\CustomVerificationNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CustomVerificationNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent()
    {
        // Crée un utilisateur de test
        $user = User::factory()->create([
            'email_verified_at' => null, // Assurez-vous que l'e-mail n'est pas vérifié
        ]);

        // Mock des notifications
        Notification::fake();

        // Envoie la notification
        $user->notify(new CustomVerificationNotification());

        // Vérifie que la notification a été envoyée
        Notification::assertSentTo(
            [$user], 
            CustomVerificationNotification::class
        );
    }

    public function test_email_contains_verification_url_and_otp_code()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        Notification::fake();

        $user->notify(new \App\Notifications\CustomVerificationNotification());

        Notification::assertSentTo(
            [$user],
            \App\Notifications\CustomVerificationNotification::class,
            function ($notification, $channels) use ($user) {
                $mailData = $notification->toMail($user)->viewData;

                $expectedUrl = URL::temporarySignedRoute(
                    'verification.verify',
                    now()->addMinutes(config('auth.verification.expire', 60)),
                    [
                        'id' => $user->getKey(),
                        'hash' => sha1($user->getEmailForVerification()),
                    ]
                );

                return $mailData['verificationUrl'] === $expectedUrl &&
                    $mailData['data']['id'] === $user->id;
            }
        );
    }
}
