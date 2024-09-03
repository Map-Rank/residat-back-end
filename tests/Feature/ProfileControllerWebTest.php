<?php

// tests/Feature/ProfileControllerTest.php

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;


class ProfileControllerWebTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    /** @test */
    public function it_can_display_the_profile_edit_form()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Envoyer une requête GET pour afficher le formulaire d'édition de profil
        $response = $this->get(route('profile.edit'));

        // Vérifier que la réponse contient la vue 'profile.edit' et les informations de l'utilisateur
        $response->assertViewIs('profile.edit');
        $response->assertSee($user->name); // Vérifiez si le nom de l'utilisateur est présent
    }

    /** @test */
    public function it_can_update_the_user_profile_information()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Générer des données de profil mises à jour avec Faker
        $updatedProfileData = [
            'first_name' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'date_of_birth' => $this->faker->date(),
            'password' => bcrypt($this->faker->password()),
            // Ajoutez d'autres champs de profil selon vos besoins
        ];

        // dd($updatedProfileData);

        // Envoyer une requête POST pour mettre à jour les informations du profil de l'utilisateur
        $response = $this->patch(route('profile.update'), $updatedProfileData);

        // Rafraîchir l'utilisateur depuis la base de données pour obtenir ses dernières informations
        $user->refresh();

        // Vérifier que la redirection a réussi vers la page de profil édité
        $response->assertRedirect(route('profile.edit'));

        // Vérifier que les informations du profil de l'utilisateur ont été mises à jour
        $this->assertEquals($updatedProfileData['first_name'], $user->first_name);
        $this->assertEquals($updatedProfileData['email'], $user->email);
        // Vérifiez d'autres champs de profil selon vos besoins
    }

    /** @test */
    public function it_can_delete_the_user_account()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        // Authentifier l'utilisateur
        $this->actingAs($user);

        // Envoyer une requête POST pour supprimer le compte utilisateur
        $response = $this->delete(route('profile.destroy'), ['password' => 'password']); // Assurez-vous que 'password' correspond au mot de passe actuel de l'utilisateur

        // Vérifier que la redirection a réussi vers la page d'accueil
        $response->assertRedirect('/');

        // Vérifier que l'utilisateur a été supprimé de la base de données
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }
}

