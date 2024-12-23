<?php

namespace Tests\Feature\Subscriptions;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Package;
use App\Models\Payment;
use App\Models\Subscription;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SubscriptionPolicyTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_allows_viewing_own_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        $this->assertTrue($user->can('view', $subscription));
    }

    /** @test */
    public function it_prevents_updating_cancelled_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'cancelled']);

        $this->assertFalse($user->can('update', $subscription));
    }

    /** @test */
    public function it_allows_canceling_active_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        $this->assertTrue($user->can('cancel', $subscription));
    }

    /** @test */
    public function it_prevents_renewing_active_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        $this->assertFalse($user->can('renew', $subscription));
    }

    /** @test */
    public function it_allows_renewing_expired_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'expired']);

        $this->assertTrue($user->can('renew', $subscription));
    }

    public function test_user_can_create_payment_for_own_subscription()
    {
        // Créer un utilisateur
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        // Créer une souscription pour cet utilisateur
        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        // Vérifier que l'utilisateur peut créer un paiement pour sa propre souscription
        $this->assertTrue(
            $user->can('createPayment', $subscription)
        );
    }

    public function test_user_cannot_create_payment_for_other_subscription()
    {
        // Créer deux utilisateurs
        $user1 = User::factory()->create(['email' => 'test1@example.com']);
        $user2 = User::factory()->create(['email' => 'test2@example.com']);

        // Créer une souscription pour le deuxième utilisateur
        $subscription = Subscription::factory()->for($user2)->create(['status' => 'active']);

        // Vérifier que l'utilisateur1 ne peut pas créer un paiement pour la souscription de user2
        $this->assertFalse(
            $user1->can('createPayment', $subscription)
        );
    }
}
