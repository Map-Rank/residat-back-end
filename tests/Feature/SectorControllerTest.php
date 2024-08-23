<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectorControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_fetch_sectors(): void
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        Sector::create(['name'=> 'Test 0']);

        $response = $this->getJson(route('sectors.index'));

        $response->assertStatus(200)
                 ->assertViewHas('sectors');
    }

    /**
     * Test the store method to ensure a new sector is created.
     *
     * @return void
     */
    public function test_store_creates_sector()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        $data = [
            'name' => 'New Sector',
        ];

        $response = $this->post(route('sectors.store'), $data);

        $response->assertRedirect(route('sectors.index'))
                 ->assertSessionHas('success', 'Sector created successfully.');

        $this->assertDatabaseHas('sectors', ['name' => 'New Sector']);
    }
    
}
