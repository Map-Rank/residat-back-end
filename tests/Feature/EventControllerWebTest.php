<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Event;
use App\Models\Sector;
use Database\Seeders\SectorSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EventControllerWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_displays_events_with_pagination()
    {
        // **Préparer les données nécessaires :**
        $admin = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$admin) {
            $admin = User::factory()->admin()->create();
        }
        
        Zone::factory()->create();

        Event::query()->delete();

        // Créer des secteurs et des zones nécessaires
        $this->seed(SectorSeeder::class); // Assurez-vous que ce seeder crée des secteurs
        Zone::factory()->count(5)->create(); // Créez plusieurs zones pour les tests
    
        // Créer des événements avec des secteurs et des zones valides
        $sectorId = Sector::inRandomOrder()->first()->id; // Obtenez un secteur valide
        $zoneId = Zone::inRandomOrder()->first()->id; // Obtenez une zone valide

        // Simuler l'authentification de l'utilisateur admin
        $this->actingAs($admin);

        Zone::factory()->create();

        // Créer 15 événements pour tester la pagination (10 par page)
        Event::factory()->count(15)->create([
            'sector_id' => $sectorId,
            'zone_id' => $zoneId,
            'user_id' => $admin->id,
        ]);

        // Simuler une requête GET vers l'index des événements
        $response = $this->get(route('evenements.index'));

        // Vérifier que la réponse a un statut 200 (OK)
        $response->assertStatus(200);

        // Vérifier que la vue retournée est celle des événements
        $response->assertViewIs('events.index');

        // Vérifier que la vue contient la pagination des événements
        $response->assertViewHas('events', function ($events) {
            return $events->count() === 10; // Vérifie que la pagination retourne 10 éléments
        });
    }
    
    public function test_admin_can_delete_event()
    {
        $admin = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$admin) {
            $admin = User::factory()->admin()->create();
        }

        Event::query()->delete();

        // Créer des secteurs et des zones nécessaires
        $this->seed(SectorSeeder::class); // Assurez-vous que ce seeder crée des secteurs
        Zone::factory()->count(5)->create(); // Créez plusieurs zones pour les tests
    
        // Créer des événements avec des secteurs et des zones valides
        $sectorId = Sector::inRandomOrder()->first()->id; // Obtenez un secteur valide
        $zoneId = Zone::inRandomOrder()->first()->id; // Obtenez une zone valide

        // Créer un événement
        $event = Event::factory()->create([
            'sector_id' => $sectorId,
            'zone_id' => $zoneId,
            'user_id' => $admin->id,
        ]);

        // Simuler l'authentification de l'utilisateur admin
        $this->actingAs($admin);

        // Simuler une requête DELETE pour supprimer l'événement
        $response = $this->delete(route('evenements.destroy', $event));

        // Vérifier que la suppression a réussi (statut 302 pour redirection)
        $response->assertStatus(302);

        // Rafraîchir le modèle pour obtenir les dernières données de la base de données
        $event->refresh();
    }

    public function test_non_admin_cannot_delete_event()
    {
        $admin = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$admin) {
            $admin = User::factory()->create();
        }

        // Créer des secteurs et des zones nécessaires
        $this->seed(SectorSeeder::class); // Assurez-vous que ce seeder crée des secteurs
        Zone::factory()->count(5)->create(); // Créez plusieurs zones pour les tests
    
        // Créer des événements avec des secteurs et des zones valides
        $sectorId = Sector::inRandomOrder()->first()->id; // Obtenez un secteur valide
        $zoneId = Zone::inRandomOrder()->first()->id; // Obtenez une zone valide

        // Créer un événement
        $event = Event::factory()->create([
            'sector_id' => $sectorId,
            'zone_id' => $zoneId,
            'user_id' => $admin->id,
        ]);

        // Simuler l'authentification de l'utilisateur non admin
        $this->actingAs($admin);

        // Attendre l'exception UnauthorizedException avec le message spécifique
        $this->expectException(\Spatie\Permission\Exceptions\UnauthorizedException::class);
        $this->expectExceptionMessage('User does not have the right roles.');

        // Tenter de supprimer l'événement en tant que non admin
        $response = $this->delete(route('evenements.destroy', $event));

        // Vérifier que l'événement existe toujours dans la base de données
        $this->assertDatabaseHas('events', [
            'id' => $event->id,
            'deleted_at' => null // Vérifie que l'événement n'a pas été supprimé
        ]);
    }
}
