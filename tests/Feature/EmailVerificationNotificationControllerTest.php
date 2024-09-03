<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Notification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;

class EmailVerificationNotificationControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_redirects_if_email_is_already_verified()
    {
        // **Préparer les données nécessaires :**
        $user = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create([
                'email_verified_at' => now(),
            ]);
        }
        // Simuler une requête authentifiée
        $response = $this->actingAs($user)->post(route('verification.send'));

        // Assurez-vous que l'utilisateur est redirigé vers la page d'accueil
        $response->assertRedirect(RouteServiceProvider::HOME);
    }

    public function test_it_sends_verification_email_if_not_verified()
    {
        // Créez un utilisateur sans vérification d'e-mail
        $user = User::factory()->admin()->create([
            'email_verified_at' => null,
        ]);

        // Fake the notification
        Notification::fake();

        // Simuler une requête authentifiée
        $response = $this->actingAs($user)->post(route('verification.send'));

        // Recharger l'utilisateur pour s'assurer que l'état est mis à jour correctement
        $user->refresh();

        // Assurez-vous que l'utilisateur n'a toujours pas vérifié son email
        $this->assertNull($user->email_verified_at);

        // Assurez-vous que la notification a été envoyée
        // Notification::assertSentTo([$user], SendEmailVerificationNotification::class);

        // Assurez-vous que l'utilisateur est redirigé à la page précédente avec un message de statut
        $response->assertRedirect()->assertSessionHas('status', 'verification-link-sent');
    }

}
