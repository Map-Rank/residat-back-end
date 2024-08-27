<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotificationTest extends TestCase
{
    use RefreshDatabase;

    public function test_notification_is_sent()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Mock des notifications
        Notification::fake();

        // Déclenche l'envoi de la notification
        $token = 'fake-token';
        $user->notify(new ResetPasswordNotification($token));

        // Vérifie que la notification a bien été envoyée
        Notification::assertSentTo(
            [$user], 
            ResetPasswordNotification::class
        );
    }

    public function test_email_contains_reset_password_link()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Mock des notifications
        Notification::fake();

        // Déclenche l'envoi de la notification
        $token = 'fake-token';
        $user->notify(new ResetPasswordNotification($token));

        // Vérifie que l'e-mail contient bien le lien de réinitialisation du mot de passe
        Notification::assertSentTo(
            [$user], 
            ResetPasswordNotification::class,
            function ($notification, $channels) use ($user, $token) {
                /** @var MailMessage $mail */
                $mail = $notification->toMail($user);

                $expectedUrl = env('FRONT_URL') . '/reset-password?token=' . $token . '&email=' . urlencode($user->email);

                return $mail->actionText === Lang::get('Reset Password') &&
                       $mail->actionUrl === $expectedUrl &&
                       str_contains($mail->introLines[0], Lang::get('You are receiving this email because we received a password reset request for your account.'));
            }
        );
    }
}
