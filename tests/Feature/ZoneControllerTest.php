<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use Illuminate\Http\JsonResponse;
use App\Http\Requests\ZoneRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;

class ZoneControllerTest extends TestCase
{
    use RefreshDatabase, InteractsWithSession;
    /**
     * A basic feature test example.
     */
    public function test_fetch_zones(): void
    {
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $level = Level::create(['name' => 'Country']);
        Zone::create(['name' => 'Test 0', 'level_id' => $level->id]);

        $response = $this->getJson(route('zone.index'));

        if (count(Zone::all()) > 0) {
            $zone = Zone::first();
            $this->assertEquals(true, $response->json()['status']);
            $this->assertEquals(1, count($response->json()['data']));
            $this->assertEquals($zone->name, $response->json()['data'][0]['name']);
        } else {
            $this->assertEquals(true, $response->json()['status']);
            $this->assertEquals(0, count($response->json()['data']));
        }
    }

    public function test_fetch_single_zone(): void
    {
        $level = Level::create(['name'=> 'Country']);
        $zone = Zone::create(['name'=> 'Test 0', 'level_id'=>$level->id]);

        $response = $this->getJson(route('zone.show', $zone->id));

        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('Test 0', ($response->json()['data']['name']));
    }

    public function testStoreZoneSuccess()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $level_id = Level::create(['name' => 'Country'])->id;
        $zone_id = Zone::create(['name' => 'Cameroun', 'level_id' => 1])->id;
        

        $validZoneData = [
            'name' => 'Test zone',
            'parent_id' => $zone_id, 
            'level_id' => $level_id, 
        ];
    
        // Appeler la route pour créer une zone
        $response = $this->postJson(route('create.zone'), $validZoneData);

        // Vérifier le code de réponse et le format JSON
        $response->assertStatus(200);
        $this->assertJson($response->getContent());

        // Vérifier si la zone a été correctement créée dans la base de données
        $this->assertDatabaseHas('zones', [
            'name' => 'Test zone',
        ]);
    }
    
    public function testStoreZoneFailsValidation()
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // **Prepare invalid zone data:**
        $invalidZoneData = [];
    
        // **Send POST request to the store endpoint:**
        $response = $this->postJson(route('create.zone'), $invalidZoneData);
    
        // **Assert validation error response:**
        $response->assertStatus(422)
        ->assertJsonValidationErrors([
            'name' => ['The name field is required.'],
            'level_id' => ['The level id field is required.']
        ]);
    
        // **Verify zone not created in database:**
        $this->assertDatabaseMissing('zones', [
            'name' => $invalidZoneData['name'] ?? null,
            'level_id' => $invalidZoneData['level_id'] ?? null
        ]);
    }

    /**
     * Test updating a zone.
     *
     * @return void
     */
    public function testUpdateZone()
    {
        // Créer un utilisateur pour l'authentification
        $user = User::factory()->create();
        $this->actingAs($user);

        // Créer une zone à mettre à jour
        $level_id = Level::create(['name' => 'Country'])->id;
        $zone_id = Zone::create(['name' => 'Cameroun', 'level_id' => 1])->id;

        $validZoneData = [
            'name' => 'Test zone',
            'parent_id' => $zone_id, 
            'level_id' => $level_id, 
        ];
    
        // Appeler la route pour créer une zone
        $response = $this->postJson(route('create.zone'), $validZoneData);

        // Données pour mettre à jour la zone
        $data = [
            'name' => 'Updated Zone Name',
            'parent_id' => $zone_id, 
            'level_id' => $level_id,
        ];

        // Appeler la route pour mettre à jour la zone
        $response = $this->putJson(route('update.zone', ['id' => $zone_id]), $data);

        $response->assertStatus(200);

        // Vérifier que la redirection a eu lieu avec un message de succès
        $response->assertRedirect();

        // Vérifier que la zone a été correctement mise à jour dans la base de données
        $this->assertDatabaseHas('zones', [
            'id' => $data->id,
            'name' => 'Updated Zone Name',
            'parent_id' => 2,
            'level_id' => 3,
            'banner' => 'path/to/updated-banner.jpg',
        ]);
    }

    /**
     * Test deleting a zone.
     *
     * @return void
     */
    public function testDestroy()
    {
        // Authentifier un utilisateur
        $user = User::factory()->create();
        $this->actingAs($user);
        
        $zone = Zone::factory()->create();

        $response = $this->deleteJson(route('delete.subdivision', $zone->id));

        $response->assertStatus(200);
            
        // Assert that the zone has been deleted from the database
        $this->assertSoftDeleted($zone);
    }

}
