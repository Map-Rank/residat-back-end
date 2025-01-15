<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class SubscriptionControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function setUp(): void
    {
        parent::setUp();

        // Seed necessary data or factories
        $this->seed();
    }

    /** @test */
    public function it_lists_subscriptions_for_authenticated_user_with_payments()
    {
        // Crée un utilisateur ou utilise un existant
        $user = User::first() ?: User::factory()->create();

        // Crée des abonnements pour l'utilisateur
        $subscriptions = Subscription::factory()
            ->count(3)
            ->for($user)
            ->create(['status' => 'active']);

        // Ajoute des paiements pour chaque abonnement
        $subscriptions->each(function ($subscription, $index) {
            $subscription->payments()->createMany([
                [
                    'amount' => 100.00,
                    'currency' => 'XAF',
                    'transaction_id' => 'TXN12345-' . $index . '-1', // Transaction ID unique
                    'payment_method' => 'mobile_money',
                    'status' => 'completed',
                    'payment_date' => now(),
                    'payment_details' => 'Test payment 1',
                ],
                [
                    'amount' => 200.00,
                    'currency' => 'XAF',
                    'transaction_id' => 'TXN12345-' . $index . '-2', // Transaction ID unique
                    'payment_method' => 'cash',
                    'status' => 'completed',
                    'payment_date' => now(),
                    'payment_details' => 'Test payment 2',
                ],
            ]);
        });

        // Agir en tant qu'utilisateur authentifié
        $this->actingAs($user);

        // Faire une requête GET à l'endpoint
        $response = $this->getJson(route('subscriptions.index'));

        // Vérifier la réponse
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data') // Vérifie le nombre d'abonnements
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'package',
                        'zone',
                        'start_date',
                        'end_date',
                        'status',
                        'notes',
                        'payments' => [
                            '*' => [
                                'id',
                                'amount',
                                'currency',
                                'transaction_id',
                                'payment_method',
                                'status',
                                'payment_date',
                                'payment_details',
                            ],
                        ],
                    ],
                ],
            ]);

        // Vérifie que les données des paiements sont correctes
        $responseData = $response->json('data');
        foreach ($responseData as $subscriptionData) {
            $this->assertCount(2, $subscriptionData['payments']); // Chaque abonnement a 2 paiements
        }
    }

    /** @test */
    public function it_creates_a_subscription_and_payment()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $package = Package::factory()->create(['price' => 1000, 'periodicity' => 'Month']);
        $zone = Zone::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson(route('subscriptions.store'), [
            'package_id' => $package->id,
            'zone_id' => $zone->id,
            'amount' => 1000,
            'payment_method' => 'online',
            'status' => 'completed',
            'transaction_id' => $this->faker->uuid(),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        $this->assertDatabaseHas('payments', [
            'amount' => 1000,
            'status' => 'completed',
            'payment_method' => 'online',
        ]);
    }

    /** @test */
    public function it_prevents_creating_a_subscription_with_an_existing_active_one()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $package = Package::factory()->create(['price' => 1000]);
        $zone = Zone::factory()->create();
        Subscription::factory()->count(3)->for($user)->create(['status' => 'active']);

        $this->actingAs($user);

        $response = $this->postJson(route('subscriptions.store'), [
            'package_id' => $package->id,
            'zone_id' => $zone->id,
            'amount' => 1000,
            'payment_method' => 'online',
            'status' => 'completed',
            'transaction_id' => $this->faker->uuid(),
        ]);

        $response->assertStatus(400)
            ->assertJson(['message' => 'You already have an active subscription']);
    }

    /** @test */
    public function it_updates_a_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        $this->actingAs($user);

        $response = $this->putJson(route('subscriptions.update', $subscription), [
            'status' => 'cancelled',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled',
        ]);
    }

    /** @test */
    public function it_cancels_an_active_subscription()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        $this->actingAs($user);

        $response = $this->patchJson(route('subscriptions.cancel', $subscription), [
            'reason' => 'No longer needed',
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'cancelled');

        $this->assertDatabaseHas('subscriptions', [
            'id' => $subscription->id,
            'status' => 'cancelled',
            'notes' => 'No longer needed',
        ]);
    }

    /** @test */
    public function it_renews_an_expired_subscription_and_creates_payment()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        // $package = Package::factory()->create(['price' => 1000, 'periodicity' => 'Month']);
        $zone = Zone::factory()->create();

        $subscription = Subscription::factory()->for($user)->create(['status' => 'expired', 'end_date' => now()->subDays(1)]);
        // Capturer l'ID de la souscription avant le renouvellement
        $oldSubscriptionId = $subscription->id;

        $this->actingAs($user);

        $response = $this->postJson(route('subscriptions.renew', $subscription), [
            'package_id' => $subscription->package->id,
            'zone_id' => $zone->id,
            'amount' => $subscription->package->price,
            'payment_method' => 'mobile_money',
            'status' => 'completed',
            'transaction_id' => $this->faker->uuid(),
        ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.status', 'active');

        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'status' => 'active',
        ]);

        // Vérifier que la nouvelle souscription a un ID différent
        $newSubscription = Subscription::where('user_id', $user->id)->where('status', 'active')->first();
        $this->assertNotEquals($oldSubscriptionId, $newSubscription->id);

        $this->assertDatabaseHas('payments', [
            'amount' => $subscription->package->price,
            'status' => 'completed',
            'payment_method' => 'mobile_money',
        ]);
    }

    /** @test */
    public function it_returns_the_current_subscription()
    {
        // Vider la table des souscriptions
        Subscription::query()->delete();

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (! $user) {
            $user = User::factory()->create();
        }

        $subscription = Subscription::factory()->for($user)->create(['status' => 'active']);

        $this->actingAs($user);

        $response = $this->getJson(route('subscriptions.current'));

        $response->assertStatus(200)
            ->assertJsonPath('data.id', $subscription->id);
    }
}
