<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class DeleteAccountTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_own_destroy_deletes_account()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }


        // Simule l'authentification de l'utilisateur
        $this->actingAs($user);

        // Vérifie si l'utilisateur a un avatar stocké
        if ($user->avatar) {
            Storage::fake('public'); // Simule le stockage en local
            Storage::disk('public')->put($user->avatar, 'dummy_avatar_content');
        }

        // Effectue la requête de suppression
        $response = $this->delete(route('delete.user'));

        // Vérifie la réponse et le message de succès
        $response->assertStatus(200)
                ->assertJsonFragment(['message' => 'User deleted successfully']);

        // Vérifie que l'utilisateur a bien été soft deleted
        $this->assertSoftDeleted('users', ['id' => $user->id]);

        // Vérifie que les relations associées ont été supprimées
        $this->assertDatabaseMissing('feedbacks', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('events', ['user_id' => $user->id]);

        // Vérifie que l'avatar a été supprimé du stockage s'il existait
        if ($user->avatar) {
            Storage::disk('public')->assertMissing($user->avatar);
        }
    }

}