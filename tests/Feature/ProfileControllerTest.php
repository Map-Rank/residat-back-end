<?php

// tests/Feature/ProfileControllerTest.php

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Interaction;
use App\Models\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class ProfileControllerTest extends TestCase
{
    /**
     * Test the profile endpoint.
     */
    public function testUserProfile()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $response = $this->actingAs($user)->get('api/profile');

        $response->assertStatus(200);
    }

    /**
     * Test the interactions endpoint.
     */
    public function testUserInteractions()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $response = $this->actingAs($user)->get('api/profile-interaction');

        $response->assertStatus(200);
    }

    public function testUserProfileUpdate()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create([
                'email' => 'updateduser@example.com', // Nouveau mail unique
            ]);
        }

        // Simuler le stockage local pour éviter les actions réelles sur le système de fichiers
        Storage::fake('public');

        // Simuler un fichier avatar
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        // Générer le nom de fichier attendu avec uniqid comme dans le contrôleur
        $avatarName = 'avatar_' . uniqid() . '.' . $avatar->getClientOriginalExtension();
        $avatarPath = 'media/avatar/' . $user->email . '/' . $avatarName;

        $response = $this->actingAs($user)->put(route('update.profile', $user->id), [
            'first_name' => 'Updated',
            'last_name' => 'User',
            'phone' => '1234567890',
            'date_of_birth' => '1990-01-01',
            'gender' => 'male',
            'profession' => 'Developer',
            'avatar' => $avatar,
        ]);

        // Vérifier que les informations de l'utilisateur ont été mises à jour
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => 'Updated',
            'last_name' => 'User',
            'email' => 'users@user.com',
        ]);

        // Vérifier qu'un fichier a été stocké dans le bon répertoire
        Storage::disk('public')->assertExists('media/avatar/' . $user->email);

        // Optionnel : vérifier que le fichier a bien été ajouté, peu importe le nom généré
        $files = Storage::disk('public')->allFiles('media/avatar/' . $user->email);
        $this->assertCount(1, $files); // Vérifie qu'un fichier a bien été sauvegardé

        $user->refresh();
        $this->assertStringContainsString('media/avatar/' . $user->email, $user->avatar);

        $response->assertStatus(200);
    }
}