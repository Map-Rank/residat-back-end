<?php

namespace Tests\Feature\Middleware;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class RedirectIfAuthenticatedTest extends TestCase
{
    public function test_authenticated_user_is_redirected_to_home()
    {
        // Assurez que la table users est vide
        User::query()->delete();

        // Créer un utilisateur et le connecter
        $user = User::factory()->admin()->create([
            "email" => "test@example.com",
        ]);

        Auth::login($user);

        // Définir une route temporaire avec le middleware 'guest'
        Route::get('/login', function () {
            return 'Login Page';
        })->middleware('guest');

        // Simuler une requête GET à cette route
        $response = $this->get('/login');

        // Vérifier la redirection vers la page d'accueil
        $response->assertRedirect(RouteServiceProvider::HOME);
    }
}
