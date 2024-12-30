<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Mail\WelcomeEmail;
use Illuminate\Support\Str;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Class LoginManagementTest
 * @package Tests\Feature
 */
class AuthControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        RateLimiter::clear($this->throttleKey('test@example.com'));
    }

    private function throttleKey($email): string
    {
        return strtolower($email) . '|' . request()->ip();
    }


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
        Mail::assertSent(WelcomeEmail::class, function ($mail) use ($data) {
            return $mail->hasTo($data['email']);
        });

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

    public function test_user_can_login_with_email()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token'
                ],
                'message',
                'status'
            ]);

        $this->assertAuthenticatedAs($user);
    }

    // Test de login avec numéro de téléphone
    public function test_user_can_login_with_phone()
    {
        $user = User::factory()->create([
            'phone' => '1234567890',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/login', [
            'email' => '1234567890', // On utilise le champ email pour le téléphone
            'password' => 'password'
        ]);

        $response->assertStatus(200);
        $this->assertAuthenticatedAs($user);
    }

    // // Test de validation des champs requis
    public function test_login_validation_rules()
    {
        $this->withExceptionHandling();

        $response = $this->postJson('/api/login', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email', 'password']);

        // Test avec fcm_token optionnel
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'fcm_token' => 'valid-token'
        ]);

        $response->assertStatus(200);
    }

    // Test de limitation de taux (rate limiting)
    public function test_rate_limiting()
    {
        $this->withExceptionHandling();

        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);
    
        // Tenter de se connecter 5 fois avec des identifiants incorrects
        for ($i = 0; $i < 5; $i++) {
            $this->postJson('/api/login', [
                'email' => 'test@example.com',
                'password' => 'wrong_password',
            ])->assertStatus(422)
              ->assertJsonValidationErrors(['email']);
        }
    
        // Faire une 6e tentative qui devrait activer le rate limiting
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password',
        ]);

        // Récupérer le nombre de secondes restantes pour la tentative suivante
        $secondsRemaining = RateLimiter::availableIn($this->throttleKey('test@example.com'));
    
        // Vérifiez le statut et le message de rate limiting
        $response->assertStatus(422) 
                 ->assertJsonFragment([
                    'email' => ["Too many login attempts. Please try again in $secondsRemaining seconds."]
                 ]);
    }

    // Test pour un utilisateur COUNCIL non vérifié
    public function test_unverified_council_user_login()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'type' => 'COUNCIL',
            'email_verified_at' => null
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'token',
                    'verified'
                ],
                'message'
            ])
            ->assertJsonPath('data.verified', false);
    }

    // Test de mise à jour du FCM token
    public function test_fcm_token_update()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        $fcmToken = 'new-fcm-token';
        
        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'fcm_token' => $fcmToken
        ]);

        $response->assertStatus(200);
        $this->assertEquals($fcmToken, $user->fresh()->fcm_token);
    }

    // Test des informations de l'utilisateur dans la réponse
    public function test_login_response_includes_user_data()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'password'
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'email',
                    'token'
                ],
                'message',
                'status'
            ]);
    }

    // Test des credentials invalides
    public function test_invalid_credentials()
    {
        $this->withExceptionHandling();

        User::factory()->create([
            'email' => 'test@example.com',
            'password' => Hash::make('password')
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'test@example.com',
            'password' => 'wrong_password'
        ]);

        $response->assertStatus(422)
        ->assertJsonValidationErrors(['email'])
        ->assertJsonFragment([
            'email' => ['These credentials do not match our records.'],
        ]);
    }
}
