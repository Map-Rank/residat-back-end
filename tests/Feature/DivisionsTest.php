<?php

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DivisionsTest extends TestCase
{
    use RefreshDatabase;
    
    public function test_divisions_with_valid_request()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        // Mocking a valid request
        $request = [
            'parent_id' => 1,
            // Add other valid parameters if needed
        ];

        // Mocking the route parameter $id
        $id = 1;

        // Calling the divisions method with the mock request and $id
        $response = $this->get(route('region.division', ['id' => $id]), $request);

        // Asserting that the status code is 200 (OK)
        $response->assertStatus(200);

        // Asserting that the divisions view is returned
        $response->assertViewIs('zones.divisions');

        // Add more assertions if needed
    }

    // public function test_divisions_with_invalid_request()
    // {
    //     // **Prepare user and necessary data:**
    //     $user = User::first();

    //     // Si aucun utilisateur n'existe, créez-en un
    //     if (!$user) {
    //         $user = User::factory()->admin()->create();
    //     }

    //     $this->actingAs($user); // Authenticate if applicable

    //     // Mocking an invalid request
    //     $request = [
    //         'parent_id' => 'invalid_id', // For example, passing a non-integer value
    //         // Add other invalid parameters if needed
    //     ];

    //     // Mocking the route parameter $id
    //     $id = '1';

    //     // Calling the divisions method with the mock request and $id
    //     $response = $this->get(route('region.division', ['id' => $id]), $request);

    //     // Asserting that the request is redirected back
    //     $response->assertStatus(200);

    //     // Asserting that the session contains the validation errors
    //     $response->assertSessionHasErrors();

    //     // Add more assertions if needed
    // }

    public function test_subdivisions_with_valid_request()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        // Mocking a valid request
        $request = [
            'parent_id' => 1,
            // Add other valid parameters if needed
        ];

        // Mocking the route parameter $id
        $id = 1;

        // Calling the subdivisions method with the mock request and $id
        $response = $this->get(route('region.division.subdivisions', ['id' => $id]), $request);

        // Asserting that the status code is 200 (OK)
        $response->assertStatus(200);

        // Asserting that the subdivisions view is returned
        $response->assertViewIs('zones.subdivisions');

        // Add more assertions if needed
    }

    // public function test_subdivisions_with_invalid_request()
    // {
    //     // **Prepare user and necessary data:**
    //     $user = User::first();

    //     // Si aucun utilisateur n'existe, créez-en un
    //     if (!$user) {
    //         $user = User::factory()->admin()->create();
    //     }

    //     $this->actingAs($user); // Authenticate if applicable

    //     // Mocking an invalid request
    //     $request = [
    //         'parent_id' => 'invalid_id', // For example, passing a non-integer value
    //         // Add other invalid parameters if needed
    //     ];

    //     // Mocking the route parameter $id
    //     $id = 1;

    //     // Calling the subdivisions method with the mock request and $id
    //     $response = $this->get(route('region.division.subdivisions', ['id' => $id]), $request);
        
    //     // Asserting that the request returns a 400 status code
    //     $response->assertStatus(400);

    //     // Add more assertions if needed
    // }

    public function test_edit_returns_edit_view_with_parent_zone_data()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Create a parent zone
        $parentZone = Zone::factory()->create();

        // Create a child zone with the same level_id as the parent
        $childZone = Zone::factory()->create([
            'parent_id' => $parentZone->id,
        ]);

        $response = $this->get(route('zone.edit', $childZone->id));

        $response->assertStatus(200);
        $response->assertViewIs('zones.edit');
        $response->assertViewHas('zones', function ($zones) use ($parentZone) {
            return $zones->count() === 1 && $zones->first()->is($parentZone);
        });
    }

    public function test_edit_returns_edit_view_with_empty_zones_when_no_parent()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $zone = Zone::factory()->create([
            'parent_id' => null,
        ]);

        $response = $this->get(route('zone.edit', $zone->id));

        $response->assertStatus(200);
        $response->assertViewIs('zones.edit');
    }

    public function test_edit_throws_model_not_found_exception_for_nonexistent_zone()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $this->get(route('zone.edit', 100)); // Nonexistent ID
    }
}