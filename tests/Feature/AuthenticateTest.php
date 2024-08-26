<?php

namespace Tests\Feature\Http\Middleware;

use Tests\TestCase;
use App\Models\User;
use App\Http\Middleware\Authenticate;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;


class AuthenticateTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function test_it_redirects_unauthenticated_users_to_login_for_web_requests()
    {
        $this->expectException(AuthenticationException::class);
        
        $response = $this->json('GET', 'api/post');

        // $response->assertStatus(401); // Unauthorized
        $response->assertJson([
            'message' => 'Unauthenticated.',
        ]);
    }

    public function test_it_allows_authenticated_users_for_web_requests()
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $response = $this->get(route('dashboard'));

        $response->assertStatus(200);
    }

    public function test_it_allows_json_requests_without_redirection()
    {
        $user = User::factory()->admin()->create();

        $this->actingAs($user);

        $response = $this->json('GET', 'api/post');

        $response->assertStatus(200); // Or expected response status code
    }
}