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


    /**
     * Test the edit method to ensure it returns a successful response.
     *
     * @return void
     */
    public function test_edit_returns_successful_response()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        $sector = Sector::factory()->create();

        $response = $this->get(route('sectors.edit', $sector->id));

        $response->assertStatus(200);
        // Note: This will need to be updated when the edit view is implemented
    }

    /**
     * Test the update method to ensure a sector is updated.
     *
     * @return void
     */
    public function test_update_updates_sector()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        $sector = Sector::factory()->create();

        $data = [
            'name' => 'Updated Sector Name',
        ];

        $response = $this->put(route('sectors.update', $sector), $data);

        $response->assertRedirect(route('sectors.index'))
                 ->assertSessionHas('success', 'Sector updated successfully.');

        $this->assertDatabaseHas('sectors', ['id' => $sector->id, 'name' => 'Updated Sector Name']);
    }

    /**
     * Test the destroy method to ensure a sector is deleted.
     *
     * @return void
     */
    public function test_destroy_deletes_sector()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        $sector = Sector::factory()->create();

        $response = $this->delete(route('sectors.destroy', $sector));

        // Vérifie que la redirection a bien eu lieu et que le message de succès est présent
        $response->assertRedirect(route('sectors.index'))
                ->assertSessionHas('success', 'Sector deleted successfully.');

        // Vérifie que le secteur a bien été soft deleted (présent dans la table mais avec deleted_at non null)
        $this->assertSoftDeleted('sectors', ['id' => $sector->id]);
    }
    
}
