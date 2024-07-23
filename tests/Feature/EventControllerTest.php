<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Event;
use App\Models\Sector;
use Laravel\Sanctum\Sanctum;
use App\Models\TypeInteraction;
use Illuminate\Http\UploadedFile;
use Database\Seeders\SectorSeeder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\Concerns\InteractsWithDatabase;

class EventControllerTest extends TestCase
{
    use RefreshDatabase, WithFaker, InteractsWithDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // $this->seed();
        Sanctum::actingAs(
            User::factory()->create($this->dataLogin())
        );
    }

    /** @test */
    public function it_can_list_events()
    {
        // **Préparer les données nécessaires :**
        $user = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }
    
        Sanctum::actingAs($user);
    
        // Créer des secteurs et des zones nécessaires
        $this->seed(SectorSeeder::class); // Assurez-vous que ce seeder crée des secteurs
        Zone::factory()->count(5)->create(); // Créez plusieurs zones pour les tests
    
        // Créer des événements avec des secteurs et des zones valides
        $sectorId = Sector::inRandomOrder()->first()->id; // Obtenez un secteur valide
        $zoneId = Zone::inRandomOrder()->first()->id; // Obtenez une zone valide
    
        Event::factory()->count(20)->create([
            'sector_id' => $sectorId,
            'zone_id' => $zoneId,
            'user_id' => $user->id,
        ]);
    
        $response = $this->getJson('/api/events?page=0&size=10');
    
        $response->assertStatus(200);
    }

    /** @test */
    public function it_validates_list_events_parameters()
    {
        $response = $this->getJson('/api/events?page=invalid&size=invalid');

        $response->assertStatus(400);
    }

    /** @test */
    public function it_can_store_event()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }
        
        Sanctum::actingAs($user);

        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = Zone::inRandomOrder()->first()->id;

        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');
        $file = UploadedFile::fake()->image('media.jpg');

        $eventData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'organized_by' => $this->faker->company(),
            'sector_id' => $sectorId,
            'user_id' => $user->id,
            'zone_id' => $zoneId,
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(1)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(2)->toDateTimeString(),
            'is_valid' => false,
            'media' => $file,
        ];

        $response = $this->postJson('/api/events', $eventData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('events', [
            'title' => $eventData['title'],
            'description' => $eventData['description'],
            'location' => $eventData['location'],
            'organized_by' => $eventData['organized_by'],
        ]);

        $imageName = time() . '.' . $file->getClientOriginalExtension();

        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
            Storage::disk('public')->assertExists('images/' . $imageName);
        } else {
            Storage::disk('s3')->assertExists('images/' . $imageName);
        }
    }

    /** @test */
    public function it_can_show_event()
    {
        // Préparez l'utilisateur et les données nécessaires :
        $user = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }
    
        // Exécuter le seeder des secteurs et créer une zone
        $this->seed(SectorSeeder::class);
        $zone = Zone::factory()->create(); // Assurez-vous d'avoir une zone existante
    
        // Récupérer un ID aléatoire d'un secteur
        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = $zone->id;
    
        // Créer un événement
        $event = Event::factory()->create([
            'sector_id' => $sectorId,
            'zone_id' => $zoneId,
            'user_id' => $user->id,
        ]);
    
        // Récupérer les informations de l'événement
        $response = $this->getJson("/api/events/{$event->id}");
    
        // Vérifier que la réponse a le bon statut
        $response->assertStatus(200);
    

    }

    /** @test */
    public function it_can_update_event()
    {
        // Préparez l'utilisateur et les données nécessaires :
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        Sanctum::actingAs($user);

        // Exécuter le seeder des secteurs
        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        // Récupérer un ID aléatoire d'un secteur
        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = Zone::inRandomOrder()->first()->id;

        // Fake Storage
        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');
        $file = UploadedFile::fake()->image('media.jpg');

        // Créer un événement initial
        $initialEventData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'organized_by' => $this->faker->company(),
            'sector_id' => $sectorId,
            'user_id' => $user->id,
            'zone_id' => $zoneId,
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(1)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(2)->toDateTimeString(),
            'is_valid' => false,
            'media' => $file,
        ];

        $initialResponse = $this->postJson('/api/events', $initialEventData);
        $initialResponse->assertStatus(201);

        $eventId = $initialResponse->json('data.id');

        // Nouvelles données pour la mise à jour
        $file = UploadedFile::fake()->image('media.jpg');
        
        $updateEventData = [
            'title' => 'Updated ' . $this->faker->sentence(),
            'description' => 'Updated ' . $this->faker->sentence(),
            'location' => 'Updated ' . $this->faker->address(),
            'organized_by' => 'Updated ' . $this->faker->company(),
            'sector_id' => $sectorId,
            'user_id' => $user->id,
            'zone_id' => $zoneId,
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(3)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(4)->toDateTimeString(),
            'is_valid' => true,
            'media' => $file,
        ];

        // Envoyer la requête de mise à jour
        $updateResponse = $this->putJson("/api/events/{$eventId}", $updateEventData);
        $updateResponse->assertStatus(200);

        // Vérifier que les données ont été mises à jour dans la base de données
        $this->assertDatabaseHas('events', [
            'id' => $eventId,
            'title' => $updateEventData['title'],
            'description' => $updateEventData['description'],
            'location' => $updateEventData['location'],
            'organized_by' => $updateEventData['organized_by'],
        ]);

        // Générer le nom de fichier attendu
        $imageName = time() . '.' . $file->getClientOriginalExtension();

        // Vérifier que le nouveau fichier média existe dans le disque approprié
        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
            Storage::disk('public')->assertExists('images/' . $imageName);
        } else {
            Storage::disk('s3')->assertExists('images/' . $imageName);
        }
    }

    /** @test */
    public function it_can_delete_event()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }
        
        Sanctum::actingAs($user);

        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = Zone::inRandomOrder()->first()->id;

        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');
        $file = UploadedFile::fake()->image('media.jpg');

        $eventData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'organized_by' => $this->faker->company(),
            'sector_id' => $sectorId,
            'user_id' => $user->id,
            'zone_id' => $zoneId,
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(1)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(2)->toDateTimeString(),
            'is_valid' => false,
            'media' => $file,
        ];

        $response = $this->postJson('/api/events', $eventData);
        $response->assertStatus(201);

        // Récupérer l'ID de l'événement créé
        $eventId = $response->json('data.id');

        $imageName = time() . '.' . $file->getClientOriginalExtension();

        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
            Storage::disk('public')->assertExists('images/' . $imageName);
        } else {
            Storage::disk('s3')->assertExists('images/' . $imageName);
        }

        // Envoyer la requête de suppression
        $response = $this->deleteJson("/api/events/{$eventId}");

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier que l'événement a été supprimé de la base de données
        $this->assertSoftDeleted('events', [
            'id' => $eventId,
        ]);

        // Vérifier que la réponse JSON est correcte
        $response->assertJson([
            'status' => true,
            'data' => [],
            'message' => 'Event deleted successfully',
        ]);

    }

    /** @test */
    public function it_prevents_unauthorized_event_deletion()
    {
        // Exécuter le seeder des secteurs et des zones
        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        // Récupérer un ID aléatoire d'un secteur et d'une zone
        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = Zone::inRandomOrder()->first()->id;

        // Créer le premier utilisateur (propriétaire de l'événement)
        $firstUser = User::factory()->create([
            'email' => 'owner@user.com',
            'password' => bcrypt('password'),
        ]);

        // Créer le deuxième utilisateur (utilisateur non autorisé)
        $secondUser = User::factory()->create([
            'email' => 'unauthorized@user.com',
            'password' => bcrypt('password'),
        ]);

        // Fake Storage
        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');
        $file = UploadedFile::fake()->image('media.jpg');

        // Créer un événement pour le premier utilisateur
        $event = [
            'user_id' => $firstUser->id,
            'sector_id' => $sectorId,
            'zone_id' => $zoneId,
            'title' => 'Initial Event',
            'description' => 'Initial description',
            'location' => 'Initial location',
            'organized_by' => 'Initial organizer',
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(1)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(2)->toDateTimeString(),
            'is_valid' => false,
            'media' => $file,
        ];

        $initialResponse = $this->postJson('/api/events', $event);
        $initialResponse->assertStatus(201);

        $eventId = $initialResponse->json('data.id');

        // Authentifier le deuxième utilisateur
        Sanctum::actingAs($secondUser);

        // Essayer de supprimer l'événement créé par le premier utilisateur
        $response = $this->deleteJson("/api/events/{$eventId}");

        // Vérifier que la suppression est refusée
        $response->assertStatus(401);

        // Vérifier que l'événement existe toujours dans la base de données
        $this->assertDatabaseHas('events', [
            'id' => $eventId,
        ]);
    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'users@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }
}