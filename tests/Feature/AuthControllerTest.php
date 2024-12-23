<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Mail\WelcomeEmail;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class LoginManagementTest
 * @package Tests\Feature
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;


    public function testLogin()
    {
        /**
         * Create a user
         */
        $user = User::factory()->create($this->dataLogin());

        $payload = ['email' => 'users@user.com', 'password' => 'password'];

        /**
         * Now we contact the end point to login
         */
        $response = $this->postJson('api/login', $payload);

        // we test the status after try to connect user we may have a status 200
        $response->assertStatus(200);

        /**
         * now we check if the current user is already authenticated
         */
        $this->assertAuthenticatedAs($user);

        /**
         * no errors in session
         */
        $response->assertSessionHasNoErrors();

        /**
         * we attest that we have this structure in the response Json
         */
        $response->assertJsonStructure([
            "data", 
            "message",
            "status",
        ]);
    }

    /**
     * @test
     */
    // public function invalid_login_credentials()
    // {
    //     /**
    //      * Create a user
    //      */
    //     User::factory()->create($this->dataLogin());

    //     /**
    //      * we send lose data
    //      */
    //     $payload = ['email' => 'fauxemail@email.com', 'password' => 'password'];

    //     /**
    //      * Now we try to connect the user with the the lose data
    //      */
    //     $response = $this->postJson('api/login', $payload);

    //     /**
    //      * we test the status after try to connect user we may have a status 422
    //      */
    //     $response->assertStatus(422);

    // }

    public function testRegister()
    {
        // Empêcher les exceptions d'être masquées
        $this->withoutExceptionHandling();

        // Simuler l'envoi d'un e-mail
        Mail::fake();

        // Simuler le fichier avatar
        $avatar = UploadedFile::fake()->image('avatar.jpg');

        // Données de test avec avatar et fcm_token
        $data = array_merge($this->dataRegister(), [
            'avatar' => $avatar,
            'fcm_token' => 'fake_fcm_token',
        ]);

        // Appel à l'API de registre
        $response = $this->postJson('/api/register', $data);

        // Vérification du succès et absence d'erreurs
        $response->assertStatus(201)
            ->assertSessionHasNoErrors();

        // Vérifiez que le mail de création de compte a bien été envoyé
        // Mail::assertSent(WelcomeEmail::class, function ($mail) use ($data) {
        //     return $mail->hasTo($data['email']);
        // });
        Mail::assertSent(WelcomeMail::class);

        $imageName = time() . '.' . $avatar->getClientOriginalExtension();

        if (env('APP_ENV') == 'local' || env('APP_ENV') == 'dev' || env('APP_ENV') == 'testing') {
            Storage::disk('public')->assertExists('avatar/' . $imageName);
        } else {
            Storage::disk('s3')->assertExists('avatar/' . $imageName);
        }

        // Vérifiez que l'utilisateur a bien été créé dans la base de données
        $this->assertDatabaseHas('users', [
            'email' => $data['email'],
            'fcm_token' => 'fake_fcm_token', // Vérification du token FCM
        ]);

        // Vérifiez que l'avatar a bien été stocké et attribué à l'utilisateur
        $user = User::where('email', $data['email'])->first();
        $this->assertNotNull($user->avatar);
        $this->assertStringContainsString('avatar/', $user->avatar);

        // Vérification que le token a été généré pour l'utilisateur
        $this->assertNotNull($user->tokens->first());
    }


    public function testLogout()
    {

        $this->withoutExceptionHandling();

        /**
         * Create a user
         */
        User::factory()->create($this->dataLogin());

        $payload = ['email' => 'users@user.com', 'password' => 'password'];

        /**
         * Now we contact the end point to login
         */
        $response = $this->postJson('api/login', $payload);

        $token = $response['data']['token'];


        // Now we logout the user just login
        $logout = $this->withHeader('Authorization', 'Bearer ' . $token)
            ->postJson('api/logout')
        ->assertStatus(200);


        /**
         * no errors in session
         */
        $logout->assertSessionHasNoErrors();

        /**
         * we attest that we have this structure in the response Json
         */
        $response->assertJsonStructure([
            "data", 
            "message",
            "status",
        ]);

    }

    /**
     * @return array
     */
    private function dataLogin()
    {
        return [
            'email' => 'users@user.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ];
    }

    private function dataRegister()
    {
        $zone = Zone::factory()->create();
        
        return [
            'id' => 1,
            'first_name' => 'users 2',
            'last_name' => 'last name 2',
            'phone' => '237698803158',
            'date_of_birth' => '1996-03-12',
            'email' => 'test@gmail.com',
            'password' => bcrypt('password!'),
            'gender' => 'female',
            'zone_id' => $zone->id,
            'active' => 1,
            'verified' => 1,
        ];
    }
}
