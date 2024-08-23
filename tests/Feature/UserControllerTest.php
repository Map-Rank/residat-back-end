<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        // $this->seed();
        Sanctum::actingAs(
            User::first()
        );
    }

    /** @test */
    public function it_can_display_a_listing_of_users()
    {
        $users = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$users) {
            $users = User::factory()->create();
        }
        // Envoyer une requête GET pour afficher la liste des utilisateurs
        $response = $this->get(route('users.index'));

        // Vérifier que la réponse contient la vue 'users.index' et les utilisateurs créés
        $response->assertViewIs('users.index');
        $response->assertSee($users->first()->name); // Vérifiez si le nom du premier utilisateur est présent
    }

    /** @test */
    public function it_can_display_the_specified_user()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        // Envoyer une requête GET pour afficher un utilisateur spécifié
        $response = $this->get(route('user.detail', ['id' => $user->id]));

        // Vérifier que la réponse contient la vue 'users.show' et les informations de l'utilisateur
        $response->assertViewIs('users.show');
        $response->assertSee($user->name); // Vérifiez si le nom de l'utilisateur est présent
    }

    /** @test */
    public function it_can_ban_a_user()
    {
        // Créer un utilisateur actif dans la base de données
        // $user = User::factory()->create(['active' => true]);
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create(['active' => true]);
        }

        // Récupérer la valeur de active avant d'appeler allowPost
        $originalActiveValue = $user->active;

        // Envoyer une requête POST pour bannir l'utilisateur
        $response = $this->post(route('ban.user', ['id' => $user->id]));

        // Rafraîchir l'utilisateur depuis la base de données pour obtenir sa dernière valeur active
        $user->refresh();

        // Vérifier que la redirection a réussi vers la page précédente
        $response->assertRedirect();

        

        // Vérifier que l'utilisateur est maintenant banni (active = false)
        $this->assertNotEquals($originalActiveValue, $user->active);
    }

    /** @test */
    public function it_can_activate_a_user()
    {
        $user = User::factory()->create(['email' => 'simpleusers@user.com', 'active' => false]);
        
        // Stocker la valeur originale de la propriété active de l'utilisateur
        $originalActiveValue = $user->active;

        // Envoyer une requête POST pour activer l'utilisateur
        $response = $this->post(route('active.user', ['id' => $user->id]));

        // Rafraîchir l'utilisateur depuis la base de données pour obtenir sa dernière valeur active
        $user->refresh();

        // Vérifier que la redirection a réussi vers la page précédente
        $response->assertRedirect();

        // Vérifier que la valeur de active a changé
        $this->assertNotEquals($originalActiveValue, $user->active);
    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'simpleusers@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }
}