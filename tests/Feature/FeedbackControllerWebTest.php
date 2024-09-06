<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Feedback;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class FeedbackControllerWebTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test l'affichage des feedbacks avec pagination.
     */
    public function test_index_displays_feedbacks_with_pagination()
    {
        // **Préparer les données nécessaires :**
        $user = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        // Créer plusieurs feedbacks
        Feedback::factory()->count(15)->create();

        // Envoyer une requête à l'index des feedbacks
        $response = $this->get(route('feedbacks.index'));

        // Vérifier que la réponse a un statut 200
        $response->assertStatus(200);

        // Vérifier que la vue retournée est celle des feedbacks
        $response->assertViewIs('feedbacks.index');

        // Vérifier que la vue contient la pagination des feedbacks
        $response->assertViewHas('feedbacks', function ($feedbacks) {
            return $feedbacks->count() === 10; // Vérifie que la pagination retourne 10 éléments
        });
    }

    /**
     * Test qu'un admin peut supprimer un feedback.
     */
    public function test_admin_can_delete_feedback()
    {
        // Créer un utilisateur avec le rôle admin
        // **Préparer les données nécessaires :**
        $admin = User::first();
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$admin) {
            $admin = User::factory()->admin()->create();
        }

        // Créer un feedback
        $feedback = Feedback::factory()->create();

        // Simuler l'authentification de l'admin
        $this->actingAs($admin);

        // Envoyer une requête pour supprimer le feedback
        $response = $this->delete(route('feedbacks.destroy', $feedback->id));

        // Vérifier que la suppression a réussi (statut 302 pour redirection)
        $response->assertStatus(302);

        // Vérifier que le feedback est marqué comme supprimé dans la base de données
        $this->assertSoftDeleted('feedbacks', [
            'id' => $feedback->id,
        ]);
    }

    /**
     * Test qu'un utilisateur non-admin ne peut pas supprimer un feedback.
     */
    public function test_non_admin_cannot_delete_feedback()
    {
        $admin = User::first();

       
    
        // Si aucun utilisateur n'existe, créez-en un
        if (!$admin) {
            $admin = User::factory()->default()->create();
        }

        // Créer un feedback
        $feedback = Feedback::factory()->create();

        // Simuler l'authentification de l'utilisateur non-admin
        $this->actingAs($admin);

        // S'assurer que l'exception est levée lorsque l'utilisateur tente de supprimer un feedback
        $this->expectException(\Spatie\Permission\Exceptions\UnauthorizedException::class);
        $this->expectExceptionMessage('User does not have the right roles.');

        // Envoyer une requête pour supprimer le feedback
        $response = $this->delete(route('feedbacks.destroy', $feedback));

        // Vérifiez que l'exception a été lancée
        $response->assertStatus(403);
    }
}
