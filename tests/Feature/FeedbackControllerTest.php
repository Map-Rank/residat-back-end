<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use App\Models\Feedback;
use App\Models\User;

class FeedbackControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test storing feedback without file.
     *
     * @return void
     */
    public function test_store_feedback_without_file()
    {
        // Crée un utilisateur
        $user = User::factory()->create();

        // Agis en tant que cet utilisateur
        $this->actingAs($user, 'sanctum');

        // Envoie une requête POST sans fichier
        $response = $this->postJson('/api/create-feedback', [
            'text' => 'This is a feedback',
            'page_link' => 'http://example.com',
            'rating' => 5
        ]);

        // Vérifie la réponse
        $response->assertStatus(201)
                 ->assertJson(['message' => 'Feedback created successfully']);

        // Vérifie que le feedback a été créé sans fichier
        $this->assertDatabaseHas('feedbacks', [
            'user_id' => $user->id,
            'text' => 'This is a feedback',
            'page_link' => 'http://example.com',
            'rating' => 5,
            'file' => null
        ]);
    }

    /**
     * Test storing feedback with file in local/dev/testing environment.
     *
     * @return void
     */
    public function test_store_feedback_with_file_in_local_dev_testing()
    {
        // Fake le disque public pour les environnements locaux/dev/testing
        Storage::fake('public');

        // Crée un utilisateur
        $user = User::factory()->create();

        // Agis en tant que cet utilisateur
        $this->actingAs($user, 'sanctum');

        // Crée un fichier simulé
        $file = UploadedFile::fake()->image('feedback.jpg');

        // Envoie une requête POST avec un fichier
        $response = $this->postJson('/api/create-feedback', [
            'text' => 'This is a feedback',
            'page_link' => 'http://example.com',
            'rating' => 5,
            'file' => $file
        ]);

        // Vérifie la réponse
        $response->assertStatus(201)
                ->assertJson(['message' => 'Feedback created successfully']);

        // Obtiens le feedback depuis la base de données pour récupérer le nom du fichier
        $feedback = Feedback::latest()->first();
        $imageName = basename($feedback->file);

        // Vérifie que le fichier a été stocké sur le disque public
        Storage::disk('public')->assertExists('feedbacks/' . $imageName);

        // Vérifie que le feedback a été créé avec le fichier
        $this->assertDatabaseHas('feedbacks', [
            'user_id' => $user->id,
            'text' => 'This is a feedback',
            'page_link' => 'http://example.com',
            'rating' => 5,
            'file' => 'feedbacks/' . $imageName
        ]);
    }
}
