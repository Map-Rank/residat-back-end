<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use Illuminate\Http\Response;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class ZoneControllerWebTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function it_returns_zones_with_valid_parameters()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $level = Level::create(['name' => 'Country']);
        Zone::create(['name' => 'Test 0', 'level_id' => $level->id]);

        $response = $this->get('/zones');

        $response->assertStatus(Response::HTTP_OK);
        $response->assertViewIs('zones.regions');
        $response->assertViewHas('zones');
    }

    /** @test */
    public function it_returns_error_with_invalid_parameters()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $response = $this->get('/zones', [
            'name' => $this->faker->word(), // Utilisation d'un mot aléatoire pour 'name'
            'parent_id' => 'invalid_parent_id', // Valeur de 'parent_id' invalide
            'level_id' => 'invalid_level_id', // Valeur de 'level_id' invalide
        ]);
        // dd($response);
        $response->assertStatus(400);
        $response->assertJson([
            'message' => __('Bad parameters'),
        ]);
    }

    /** @test */
    public function it_filters_zones_by_name()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $level = Level::create(['name' => 'Country']);
        $zone = Zone::create(['name' => 'Test 0', 'level_id' => $level->id]);

        $response = $this->get('/zones', ['name' => 'Test']);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($zone->name);
    }

    /** @test */
    public function it_filters_zones_by_parent_id()
    {
        // **Prepare user and necessary data:**
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $level = Level::create(['name' => 'Country']);
        $zone = Zone::create(['name' => 'Test 0', 'level_id' => $level->id]);

        $parentZone = Zone::first();

        $response = $this->get('/zones', ['parent_id' => $parentZone->id]);

        $response->assertStatus(Response::HTTP_OK);
        $response->assertSee($zone->name);
    }

    /** @test */
    public function it_can_show_create_form_with_levels()
    {
         // **Prepare user and necessary data:**
         $user = User::first();

         // Si aucun utilisateur n'existe, créez-en un
         if (!$user) {
             $user = User::factory()->create();
         }
 
         $this->actingAs($user); // Authenticate if applicable
         
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $level = Level::create(['name' => 'Country']);
        $zone = Zone::create(['name' => 'Test 0', 'level_id' => $level->id]);

        // Appelez la méthode create() du ZoneController
        $response = $this->get(route('zone.create'));

        // Vérifiez que la réponse contient la vue 'zones.create'
        $response->assertViewIs('zones.create');

        $response->assertSee($level->name);
    }
}