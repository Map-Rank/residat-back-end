<?php

namespace Tests\Feature;

// use IlluminateFoundationTestingRefreshDatabase;
use Tests\TestCase;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SectorControllerApiTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_can_fetch_all_sectors()
    {
        // Arrange
        $sectors = Sector::factory()->count(3)->create();

        // Act
        $response = $this->getJson(route('sector.index'));

        // Assert
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'created_at',
                    ],
                ],
                'message',
            ])
            ->assertJsonFragment([
                'message' => __('Values found'),
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    /** @test */
    public function it_can_filter_sectors_by_name()
    {
        // Arrange
        $sector1 = Sector::factory()->create(['name' => 'Technology']);
        $sector2 = Sector::factory()->create(['name' => 'Healthcare']);

        // Act
        $response = $this->getJson(route('sector.index', ['name' => 'Tech']));

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $sector1->id,
                'name' => $sector1->name,
            ])
            ->assertJsonMissing([
                'id' => $sector2->id,
                'name' => $sector2->name,
            ]);
    }

    /** @test */
    public function it_validates_invalid_parameters_for_index()
    {
        // Act
        $response = $this->getJson(route('sector.index', ['name' => 123]));

        // Assert
        $response->assertJsonFragment([
                'message' => __('Bad parameters'),
            ]);
    }

    /** @test */
    public function it_can_fetch_a_specific_sector()
    {
        // Arrange
        $sector = Sector::factory()->create();

        // Act
        $response = $this->getJson(route('sector.show', ['id' => $sector->id]));

        // Assert
        $response->assertStatus(200)
            ->assertJsonFragment([
                'id' => $sector->id,
                'name' => $sector->name,
            ]);
    }

    /** @test */
    public function it_returns_404_for_non_existent_sector()
    {
        // Act
        $response = $this->getJson(route('sector.show', ['id' => 9999]));

        // Assert
        $response->assertStatus(404)
            ->assertJsonFragment([
                'message' => __('Zone not found'),
            ]);
    }
}
