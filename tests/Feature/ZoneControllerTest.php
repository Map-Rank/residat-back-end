<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use App\Http\Requests\ZoneRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithSession;

class ZoneControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, InteractsWithSession;
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
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        Storage::fake(strcmp(env('APP_ENV'), 'local') == 0 || strcmp(env('APP_ENV'), 'dev') == 0 || strcmp(env('APP_ENV'), 'testing') == 0 ? 'public' : 's3');

        $level = Level::factory()->create(['name' => 'Country']);
        
        DB::table('zones')->insert([
            'name' => $this->faker->word(),
            'parent_id' => null,
            'level_id' => $level->id,
        ]);
        $parentZone = Zone::first(); // Ensure valid parent
        

        // **Create valid zone data:**
        $validZoneData = [
            'name' => 'Test Zone',
            'parent_id' => $parentZone->id ?? null,
            'level_id' => $level->id,
        ];

            // **Simulate file upload if applicable:**
            // if ($this->usesFileUpload()) {
                // Replace with your logic to prepare a mock file or use a package like "intervention/testing"
                $file = UploadedFile::fake()->image('photo.jpg');
                $validZoneData['data'] = $file;
            // }

        // **Send POST request to store zone:**
        $response = $this->postJson(route('zone.store'), $validZoneData);

        // $this->assertSessionHas('success', 'Zone Test Zone created successfully!');
        $this->assertTrue(session()->has('success'), 'Zone'.$validZoneData['name'].' created successfully!');

         // Vérifiez que la redirection s'est effectuée vers la route 'zones.index'
        $response->assertRedirect(route('zones.index'));
    }
    
    public function testStoreZoneFailsValidation()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }
        
        $this->actingAs($user);

        // **Prepare invalid zone data:**
        $invalidZoneData = [];
    
        // **Send POST request to the store endpoint:**
        $response = $this->postJson(route('zone.store'), $invalidZoneData);
    
        // **Assert validation error response:**
        $response->assertStatus(422)
        ->assertJson([
            'status' => false,
            'errors' => [
                'name' => ['The name field is required.'],
            ],
            'message' => 'Validation errors',
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
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        Level::factory()->create(['name' => 'Country']);

        $level = Level::first();
        
        DB::table('zones')->insert([
            'name' => $this->faker->word(),
            'parent_id' => null,
            'level_id' => $level->id,
        ]);

        $parentZone = Zone::first(); // Ensure valid parent

        $validZoneData = [
            'name' => 'Test zone',
            'parent_id' => $parentZone->id, 
            'level_id' => $level->id, 
        ];

        // Appeler la route pour créer une zone
        $response = $this->postJson(route('zone.store'), $validZoneData);

        // Données pour mettre à jour la zone
        $data = [
            'name' => 'Updated Zone Name',
            'parent_id' => $parentZone->id, 
            'level_id' => $level->id,
        ];

        // Appeler la route pour mettre à jour la zone
        $response = $this->putJson(route('zone.update', ['id' => $parentZone->id]), $data);

        // Vérifier le message de succès dans la session
        $this->assertTrue(session()->has('success'), 'Zone Updated Zone Name updated successfully!');

        // Vérifier que la redirection s'est effectuée vers la route 'zones.index'
        $response->assertRedirect(route('zones.index'));

        // Vérifier que la zone a été correctement mise à jour dans la base de données
        $this->assertDatabaseHas('zones', [
            'id' => $parentZone->id,
            'name' => 'Updated Zone Name',
            'parent_id' => $parentZone->id, // Assurez-vous de mettre à jour cela si nécessaire
            'level_id' => $level->id, // Assurez-vous de mettre à jour cela si nécessaire
            // Ajoutez d'autres champs si nécessaire
        ]);
    }

    /**
     * Test deleting a zone.
     *
     * @return void
     */
    public function testDestroy()
    {
        // Créer un utilisateur pour l'authentification
        $user = User::first();
        $this->actingAs($user);

        // Créer une zone à supprimer
        $zone = Zone::factory()->create();

        // Appeler la route pour supprimer la zone
        $response = $this->get(route('delete.subdivision', ['id' => $zone->id]));

        // Vérifier que la redirection s'est effectuée
        $response->assertRedirect();

        // Vérifier que la zone a été supprimée de la base de données
        $this->assertSoftDeleted($zone);

        // Vérifier que le message de succès est présent dans la session
        $this->assertTrue(session()->has('success'), 'Zone successfully deleted!');
    }

}
