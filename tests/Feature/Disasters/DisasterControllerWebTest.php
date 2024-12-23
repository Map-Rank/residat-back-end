<?php

namespace Tests\Feature\Disasters;

// use IlluminateFoundationTestingRefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Disaster;
use App\Models\Level;
use Illuminate\Foundation\Testing\RefreshDatabase;

class DisasterControllerWebTest extends TestCase
{
    use RefreshDatabase;

    public function test_disaster_index()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $disasters = Disaster::factory(3)->create();

        $response = $this->actingAs($user)->get(route('disasters.index'));

        $response->assertStatus(200)
                 ->assertViewIs('disasters.index')
                 ->assertViewHas('disasters', $disasters);
    }

    public function test_disaster_create()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $response = $this->actingAs($user)->get(route('disasters.create'));

        $response->assertStatus(200)
                 ->assertViewIs('disasters.create')
                 ->assertViewHas('zones')  // Zone must be passed to the view
                 ->assertViewHas('levels')  // Levels must be passed to the view
                 ->assertViewHas('types');  // Types must be passed to the view
    }

    public function test_disaster_store()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $zone = Zone::factory()->create();
        $disasterData = [
            'description' => 'Test Disaster',
            'locality' => 'Test Locality',
            'latitude' => 12.34,
            'longitude' => 56.78,
            'zone_id' => $zone->id,
            'level' => 1,
            'type' => 'FLOOD',
            'start_period' => '2024-01-01',
            'end_period' => '2024-12-31',
        ];

        $response = $this->actingAs($user)->post(route('disasters.store'), $disasterData);

        $response->assertRedirect(route('disasters.index'))
                 ->assertSessionHas('success', 'Disaster created successfully.');
    }

    public function test_disaster_show()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $disaster = Disaster::factory()->create();

        $response = $this->actingAs($user)->get(route('disasters.show', $disaster));

        $response->assertStatus(200)
                 ->assertViewIs('disasters.show')
                 ->assertViewHas('disaster', $disaster);
    }

    public function test_disaster_edit()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $disaster = Disaster::factory()->create();

        $response = $this->actingAs($user)->get(route('disasters.edit', $disaster));

        $response->assertStatus(200)
                 ->assertViewIs('disasters.edit')
                 ->assertViewHas('disaster', $disaster)
                 ->assertViewHas('zones')  // Zone should be available for the edit view
                 ->assertViewHas('levels')  // Levels should be available for the edit view
                 ->assertViewHas('types');  // Types should be available for the edit view
    }

    public function test_disaster_update()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $zone = Zone::factory()->create();
        $level = Level::factory()->create();

        $disaster = Disaster::factory()->create();
        $disasterData = [
            'description' => 'Updated Disaster',
            'locality' => 'Updated Locality',
            'latitude' => 12.45,
            'longitude' => 56.79,
            'zone_id' => $zone->id,
            'level' => $level->id,
            'type' => 'DROUGHT',
            'start_period' => '2024-02-01',
            'end_period' => '2024-11-30',
        ];

        $response = $this->actingAs($user)->put(route('disasters.update', $disaster), $disasterData);

        $response->assertRedirect(route('disasters.index'))
                 ->assertSessionHas('success', 'Disaster updated successfully.');
    }

    public function test_disaster_destroy()
    {
        $user = User::first();

        if (! $user) {
            $user = User::factory()->admin()->create();
        }

        $disaster = Disaster::factory()->create();

        $response = $this->actingAs($user)->delete(route('disasters.destroy', $disaster));

        $response->assertRedirect(route('disasters.index'))
                 ->assertSessionHas('success', 'Disaster deleted successfully.');
    }
}
