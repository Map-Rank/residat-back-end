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
        $user = User::first();
        // dd($user->id);
        Sanctum::actingAs($user);
        
        Event::factory()->count(20)->create();

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
        // dd($user->id);
        Sanctum::actingAs($user);

        // Exécuter le seeder des secteurs
        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        // Récupérer un ID aléatoire d'un secteur
        $sectorId = Sector::inRandomOrder()->first()->id;
        
        $ZoneId = Zone::inRandomOrder()->first()->id;

        Storage::fake('public');
        $file = UploadedFile::fake()->image('media.jpg');

        $eventData = [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'location' => $this->faker->address(),
            'organized_by' => $this->faker->company(),
            'sector_id' => $sectorId,
            'user_id' => $user->id,
            'zone_id' => $ZoneId,
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
            'organized_by' => $eventData['organized_by']
        ]);

        Storage::disk('public')->assertExists('media/events/'.$user->email.'/'.$file->hashName());
    }

    /** @test */
    public function it_can_show_event()
    {
        $event = Event::factory()->create();

        $response = $this->getJson("/api/events/{$event->id}");

        $response->assertStatus(200);
    }

    /** @test */
    public function it_can_update_event()
    {
        $user = User::first();
        Sanctum::actingAs($user);

        // Exécuter le seeder des secteurs
        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        // Récupérer un ID aléatoire d'un secteur
        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = Zone::inRandomOrder()->first()->id;

        Storage::fake('public');
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
        Storage::fake('public');
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

        // Vérifier que le nouveau fichier média existe dans le disque public
        Storage::disk('public')->assertExists('media/events/'.$user->email.'/'.$file->hashName());
        // Storage::disk('public')->assertExists('media/events'.$user->email.'/'.$newFile->hashName());
    }

    /** @test */
    public function it_can_delete_event()
    {
        $user = User::first();
        Sanctum::actingAs($user);

        // Exécuter le seeder des secteurs et des zones
        $this->seed(SectorSeeder::class);
        Zone::factory()->create();

        // Récupérer un ID aléatoire d'un secteur et d'une zone
        $sectorId = Sector::inRandomOrder()->first()->id;
        $zoneId = Zone::inRandomOrder()->first()->id;

        // Créer un événement initial
        $event = [
            'title' => 'Initial Event',
            'description' => 'Initial description',
            'location' => 'Initial location',
            'organized_by' => 'Initial organizer',
            'sector_id' => $sectorId,
            'user_id' => $user->id,
            'zone_id' => $zoneId,
            'published_at' => Carbon::now()->toDateTimeString(),
            'date_debut' => Carbon::now()->addDays(1)->toDateTimeString(),
            'date_fin' => Carbon::now()->addDays(2)->toDateTimeString(),
            'is_valid' => false,
            'media' => UploadedFile::fake()->image('initial_media.jpg'),
        ];

        $initialResponse = $this->postJson('/api/events', $event);
        $initialResponse->assertStatus(201);

        $eventId = $initialResponse->json('data.id');

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
            'media' => UploadedFile::fake()->image('initial_media.jpg'),
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