<?php

// tests/Feature/ProfileControllerTest.php

use Tests\TestCase;
use App\Models\User;

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
            $user = User::factory()->create();
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
            $user = User::factory()->create();
        }

        $response = $this->actingAs($user)->get('api/profile-interaction');

        $response->assertStatus(200);
    }
}