<?php

namespace Tests\Feature;

use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectorControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_fetch_sectors(): void
    {
        Sector::create(['name'=> 'Test 0']);

        $response = $this->getJson(route('sector.index'));

        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals(1, count($response->json()['data']));
    }

    public function test_fetch_single_sector(): void
    {

        $sector = Sector::create(['name'=> 'Test 0',]);

        $response = $this->getJson(route('sector.show', $sector->id));

        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('Test 0', ($response->json()['data']['name']));
    }
}
