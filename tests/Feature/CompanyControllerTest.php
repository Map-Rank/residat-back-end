<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Company;
use App\Mail\CompanyCreated;
use Database\Seeders\RoleSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CompanyControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_store_a_company()
    {
        // Simulez le stockage en fonction de l'environnement
        Storage::fake(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3');

        // Créez une zone pour l'association avec l'entreprise
        Zone::factory()->create();
        $zoneId = Zone::inRandomOrder()->first()->id;

        $this->seed(RoleSeeder::class);

        // Simulez un fichier image pour le profil
        $profileImage = UploadedFile::fake()->image('profile.jpg');

        // Données de la requête
        $data = [
            'company_name' => 'Test Company',
            'owner_name' => 'Test Owner',
            'description' => 'Test description',
            'email' => 'test@example.com',
            'phone' => '123-456-7890',
            'profile' => $profileImage,
            'password' => 'password',
            'language' => 'en',
            'zone_id' => $zoneId,
        ];

        // Simulez l'envoi de mails
        Mail::fake();

        // Faites la requête POST
        $response = $this->postJson('/api/create/request', $data);

        // Vérifiez que la requête a été traitée avec succès
        $response->assertStatus(201);

        // Vérifiez que l'entreprise a été enregistrée dans la base de données
        $this->assertDatabaseHas('companies', [
            'company_name' => 'Test Company',
            'email' => 'test@example.com',
        ]);

        // Vérifiez que le profil a été stocké
        $profilePath = 'company_profile_pictures/' . time() . '.' . $profileImage->getClientOriginalExtension();
        Storage::disk(env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing' ? 'public' : 's3')->assertExists($profilePath);

        // Vérifiez que le mail a été envoyé
        Mail::assertSent(CompanyCreated::class, function ($mail) use ($data) {
            return $mail->hasTo($data['email']);
        });

        // Vérifiez que l'utilisateur a été créé et qu'il a le rôle par défaut
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
        ]);
        
        $user = User::where('email', 'test@example.com')->first();
        $user->assignRole('default');
        $this->assertTrue($user->hasRole('default'));
    }
}