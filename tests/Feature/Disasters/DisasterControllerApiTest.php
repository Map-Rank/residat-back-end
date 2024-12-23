<?php

namespace Tests\Feature\Disasters;

// use IlluminateFoundationTestingRefreshDatabase;
use App\Models\Disaster;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DisasterControllerApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_desasters_index()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $disasters = Disaster::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('disasters.list'));

        $response->assertStatus(200); // Vérifie que l'ID du premier désastre correspond
    }

    public function test_desasters_show()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $disaster = Disaster::factory()->create();

        $this->actingAs($user);

        $response = $this->getJson(route('disaster.show', $disaster));

        $response->assertStatus(200)
                 ->assertJsonPath('data.id', $disaster->id);
    }

    // public function test_store()
    // {
    //     $user = User::first();

    //     // Si aucun utilisateur n'existe, créez-en un
    //     if (! $user) {
    //         $user = User::factory()->create();
    //     }

    //     $disasterData = [
    //         'description' => 'Test Disaster',
    //         'locality' => 'Test Locality',
    //         'latitude' => 12.34,
    //         'longitude' => 56.78,
    //         'zone_id' => 1,
    //         'level' => 1,
    //         'type' => 'FLOOD',
    //         'start_period' => '2024-01-01',
    //         'end_period' => '2024-12-31',
    //     ];

    //     $response = $this->actingAs($user)->postJson(route('api.disasters.store'), $disasterData);

    //     $response->assertStatus(201)
    //              ->assertJsonPath('data.description', 'Test Disaster')
    //              ->assertJsonPath('data.locality', 'Test Locality');
    // }

    // public function test_update()
    // {
    //     $user = User::first();

    //     // Si aucun utilisateur n'existe, créez-en un
    //     if (! $user) {
    //         $user = User::factory()->create();
    //     }

    //     $disaster = Disaster::factory()->create();
    //     $disasterData = [
    //         'description' => 'Updated Disaster',
    //         'locality' => 'Updated Locality',
    //         'latitude' => 12.45,
    //         'longitude' => 56.79,
    //         'zone_id' => 1,
    //         'level' => 1,
    //         'type' => 'DROUGHT',
    //         'start_period' => '2024-02-01',
    //         'end_period' => '2024-11-30',
    //     ];

    //     $response = $this->actingAs($user)->putJson(route('api.disasters.update', $disaster), $disasterData);

    //     $response->assertStatus(200)
    //              ->assertJsonPath('data.description', 'Updated Disaster');
    // }

    // public function test_destroy()
    // {
    //     $user = User::first();

    //     // Si aucun utilisateur n'existe, créez-en un
    //     if (! $user) {
    //         $user = User::factory()->create();
    //     }
        
    //     $disaster = Disaster::factory()->create();

    //     $response = $this->actingAs($user)->deleteJson(route('api.disasters.destroy', $disaster));

    //     $response->assertStatus(200)
    //              ->assertJsonPath('message', 'Disaster deleted successfully.');
    // }
}
