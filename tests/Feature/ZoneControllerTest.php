<?php

namespace Tests\Feature;

use App\Models\Level;
use App\Models\Zone;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ZoneControllerTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_fetch_zones(): void
    {
        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        $level = Level::create(['name' => 'Country']);
        Zone::create(['name' => 'Test 0', 'level_id' => $level->id]);

        $response = $this->getJson(route('zone.index'));

        if (count(Zone::all()) > 0) {
            $zone = Zone::first();
            $this->assertEquals(true, $response->json()['status']);
            $this->assertEquals(1, count($response->json()['data']));
            $this->assertEquals($zone->name, $response->json()['data'][0]['name']);
        } else {
            $this->assertEquals(true, $response->json()['status']);
            $this->assertEquals(0, count($response->json()['data']));
        }
    }

    public function test_fetch_single_zone(): void
    {
        $level = Level::create(['name'=> 'Country']);
        $zone = Zone::create(['name'=> 'Test 0', 'level_id'=>$level->id]);

        $response = $this->getJson(route('zone.show', $zone->id));

        $this->assertEquals(true, $response->json()['status']);
        $this->assertEquals('Test 0', ($response->json()['data']['name']));
    }

}
