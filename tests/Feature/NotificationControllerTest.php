<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\Zone;
use App\Models\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_notifications()
    {
        // Crée un utilisateur et une notification
        $user = User::factory()->create();
        $zone = Zone::factory()->create();
        $notification = Notification::factory()->create(['user_id' => $user->id, 'zone_id' => $zone->id]);

        // Agit en tant que cet utilisateur
        $this->actingAs($user, 'sanctum');

        $response = $this->getJson('/api/notifications?page=0&size=10');

        $response->assertStatus(200)
                 ->assertJsonFragment(['message'=>'Notifications charged successfully']);
    }

    /** @test */
    public function it_cannot_list_notifications_without_authentication()
    {
        // Désactive le middleware pour ce test spécifique
        $this->withoutMiddleware();
        
        $response = $this->getJson('/api/notifications?page=0&size=10');

        $response->assertStatus(403)->assertJsonFragment(['message'=>'User not authenticated']);
    }

    // /** @test */
    // public function it_can_create_a_notification_with_valid_data()
    // {
    //     // Fake le disque public pour les environnements locaux/dev/testing
    //     Storage::fake('public');

    //     // Crée un utilisateur et une zone
    //     $user = User::factory()->create();
    //     $zone = Zone::factory()->create();

    //     // Agit en tant que cet utilisateur
    //     $this->actingAs($user, 'sanctum');

    //     // Crée un fichier simulé
    //     Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');
    //     $file = UploadedFile::fake()->image('notification_image.jpg');

    //     // Envoie une requête POST pour créer une notification
    //     $response = $this->postJson('/api/notifications', [
    //         'titre_en' => 'Test Title EN',
    //         'titre_fr' => 'Test Title FR',
    //         'firebase_id' => 'firebase_id',
    //         'zone_id' => $zone->id,
    //         'content_en' => 'Test Content EN',
    //         'content_fr' => 'Test Content FR',
    //         'image' => Storage::url($file),
    //     ]);

    //     // Vérifie la réponse
    //     $response->assertStatus(200)
    //              ->assertJson(['message' => 'Notification created successfully']);

    //     // Vérifie que le fichier a été stocké
    //     if (env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
    //     Storage::disk('public')->assertExists('media/notifications/' . $user->email . '/' . $file->hashName());
    //     }else{
    //         Storage::disk('s3')->assertExists('media/notifications/' . $user->email . '/' . $file->hashName());
    //     }

    //     // Vérifie que la notification a été créée avec les données correctes
    //     $this->assertDatabaseHas('notifications', [
    //         'titre_en' => 'Test Title EN',
    //         'titre_fr' => 'Test Title FR',
    //         'firebase_id' => 'firebase_id',
    //         'zone_id' => $zone->id,
    //         'content_en' => 'Test Content EN',
    //         'content_fr' => 'Test Content FR',
    //         'image' => 'media/notifications/' . $user->email . '/' . $file->hashName(),
    //         'user_id' => $user->id,
    //     ]);
    // }

    // /** @test */
    // public function it_cannot_create_notification_without_authentication()
    // {
    //     $zone = Zone::factory()->create();

    //     $response = $this->postJson('/api/notifications', [
    //         'titre_en' => 'Test Title EN',
    //         'titre_fr' => 'Test Title FR',
    //         'firebase_id' => 'firebase_id',
    //         'zone_id' => $zone->id,
    //         'content_en' => 'Test Content EN',
    //         'content_fr' => 'Test Content FR',
    //     ]);

    //     $response->assertStatus(403)
    //              ->assertJson(['message' => 'User not authenticated']);
    // }

    // /** @test */
    // public function it_can_show_a_notification()
    // {
    //     // Crée une notification
    //     $notification = Notification::factory()->create();

    //     // Crée un utilisateur
    //     $user = User::factory()->create();

    //     // Agit en tant que cet utilisateur
    //     $this->actingAs($user, 'sanctum');

    //     // Envoie une requête GET pour afficher une notification
    //     $response = $this->getJson('/api/notifications/' . $notification->id);

    //     // Vérifie la réponse
    //     $response->assertStatus(200)
    //              ->assertJsonFragment(['titre_en' => $notification->titre_en]);
    // }

    // /** @test */
    // public function it_can_update_a_notification()
    // {
    //     // Crée une notification
    //     $notification = Notification::factory()->create();

    //     // Crée un utilisateur
    //     $user = User::factory()->create();

    //     // Agit en tant que cet utilisateur
    //     $this->actingAs($user, 'sanctum');

    //     // Envoie une requête PUT pour mettre à jour la notification
    //     $response = $this->putJson('/api/notifications/' . $notification->id, [
    //         'titre_en' => 'Updated Title EN',
    //         'titre_fr' => 'Updated Title FR',
    //         'firebase_id' => 'updated_firebase_id',
    //         'content_en' => 'Updated Content EN',
    //         'content_fr' => 'Updated Content FR',
    //     ]);

    //     // Vérifie la réponse
    //     $response->assertStatus(200)
    //              ->assertJson(['message' => 'Notification updated successfully']);

    //     // Vérifie que la notification a été mise à jour dans la base de données
    //     $this->assertDatabaseHas('notifications', [
    //         'id' => $notification->id,
    //         'titre_en' => 'Updated Title EN',
    //         'titre_fr' => 'Updated Title FR',
    //         'firebase_id' => 'updated_firebase_id',
    //         'content_en' => 'Updated Content EN',
    //         'content_fr' => 'Updated Content FR',
    //     ]);
    // }

    // /** @test */
    // public function it_can_delete_a_notification_if_admin()
    // {
    //     // Crée une notification
    //     $notification = Notification::factory()->create();

    //     // Crée un utilisateur admin
    //     $admin = User::factory()->create(['role' => 'admin']);

    //     // Agit en tant qu'utilisateur admin
    //     $this->actingAs($admin, 'sanctum');

    //     // Envoie une requête DELETE pour supprimer la notification
    //     $response = $this->deleteJson('/api/notifications/' . $notification->id);

    //     // Vérifie la réponse
    //     $response->assertStatus(200)
    //              ->assertJson(['message' => 'Notification deleted successfully']);

    //     // Vérifie que la notification a été supprimée de la base de données
    //     $this->assertDatabaseMissing('notifications', [
    //         'id' => $notification->id
    //     ]);
    // }

    // /** @test */
    // public function it_cannot_delete_a_notification_if_not_admin()
    // {
    //     // Crée une notification
    //     $notification = Notification::factory()->create();

    //     // Crée un utilisateur non-admin
    //     $user = User::factory()->create(['role' => 'user']);

    //     // Agit en tant que cet utilisateur
    //     $this->actingAs($user, 'sanctum');

    //     // Envoie une requête DELETE pour supprimer la notification
    //     $response = $this->deleteJson('/api/notifications/' . $notification->id);

    //     // Vérifie la réponse
    //     $response->assertStatus(403)
    //              ->assertJson(['message' => 'Unauthorized']);
    // }
}
