<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AdminDeleteAccountTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_web_destroy_deletes_account()
    {
        // Créer un utilisateur pour les tests
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        // Simuler l'authentification de l'utilisateur si nécessaire
        $this->actingAs($user);

        // Vérifie si l'utilisateur a un avatar stocké
        if ($user->avatar) {
            Storage::fake('public'); // Simule le stockage en local
            Storage::disk('public')->put($user->avatar, 'dummy_avatar_content');
        }

        // Effectue la requête de suppression
        $response = $this->delete(route('users.delete', ['id' => $user->id]));

        // Vérifie la réponse et le message de succès
        $response->assertRedirect(route('users.index'))
                ->assertSessionHas('success', 'Utilisateur supprimé avec succès');

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