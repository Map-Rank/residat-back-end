<?php

// tests/Feature/ProfileControllerTest.php

use Tests\TestCase;
use App\Models\User;
use App\Models\Event;
use App\Models\Feedback;
use App\Models\Interaction;
use App\Models\Notification;
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
}