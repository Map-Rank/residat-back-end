<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Notification;
use App\Service\UtilService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class NotificationControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_list_notifications()
    {
        // Crée un utilisateur admin
        $user = User::factory()->admin()->create();
        
        // Crée une zone et assigne-la à l'utilisateur
        $zone = Zone::factory()->create();
        $user->zone_id = $zone->id;
        $user->save();
        
        // Crée des notifications pour ce user et sa zone
        $notifications = Notification::factory()->count(5)->create([
            'user_id' => $user->id,
            'zone_id' => $zone->id,
        ]);
        
        // Agit en tant que cet utilisateur
        $this->actingAs($user, 'sanctum');
        
        // Fait la requête pour obtenir les notifications
        $response = $this->getJson('/api/notifications?page=0&size=10');

        // Vérifie le statut de la réponse et le message JSON attendu
        $response->assertStatus(200)
                ->assertJsonFragment(['message'=>'Notifications charged successfully']);
        
        // Vérifie que les notifications sont retournées
        $response->assertJsonCount(5, 'data'); // Assure que 5 notifications sont présentes dans la réponse
    }

    /** @test */
    public function it_cannot_list_notifications_without_authentication()
    {
        // Désactive le middleware pour ce test spécifique
        $this->withoutMiddleware();
        
        $response = $this->getJson('/api/notifications?page=0&size=10');

        $response->assertStatus(403)->assertJsonFragment(['message'=>'User not authenticated']);
    }

    /** @test */
    public function test_store_creates_notification()
    {
        // Créer un utilisateur avec le rôle COUNCIL
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->council()->create();
        }

        // Créer une zone
        $zone = Zone::factory()->create();

        // Simuler la requête avec des données valides
        $data = [
            'titre_en' => 'Test Notification Title EN',
            'titre_fr' => 'Test Notification Title FR',
            'firebase_id' => null,
            'zone_id' => $zone->id,
            'content_en' => 'This is a test notification content EN',
            'content_fr' => 'This is a test notification content FR',
        ];

        // Simuler l'authentification de l'utilisateur
        $this->actingAs($user);

        // Simuler l'upload d'une image
        $file = UploadedFile::fake()->image('notification.jpg');

        // Créer le nom d'image basé sur le temps
        $imageName = time().'.'.$file->getClientOriginalExtension();

        // Ajouter l'image à la requête
        $data['image'] = $file;

        // Appeler la méthode store
        $response = $this->postJson(route('notifications.store'), $data);
        // dd($response);

        // Vérifier le statut de la réponse
        $response->assertStatus(200);

        // Vérifier que la notification a été créée dans la base de données
        $this->assertDatabaseHas('notifications', [
            'titre_en' => 'Test Notification Title EN',
            'titre_fr' => 'Test Notification Title FR',
            'firebase_id' => null,
            'zone_id' => $zone->id,
            'content_en' => 'This is a test notification content EN',
            'content_fr' => 'This is a test notification content FR',
            'user_id' => $user->id,
            'image' => Storage::url('notifications/' . $imageName),
        ]);

        // Vérifier que le fichier a été stocké
        Storage::disk('public')->assertExists('notifications/' . $imageName);
    }


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
    //     $user = User::factory()->admin()->create();

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
    //     $user = User::factory()->admin()->create();

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
