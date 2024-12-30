<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Zone;
use App\Service\UtilService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilServiceTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_all_ascendants_of_a_zone()
    {
        // Crée une hiérarchie de zones : GrandParent -> Parent -> Child
        $grandParent = Zone::factory()->create(['name' => 'GrandParent Zone']);
        $parent = Zone::factory()->create(['name' => 'Parent Zone', 'parent_id' => $grandParent->id]);
        $child = Zone::factory()->create(['name' => 'Child Zone', 'parent_id' => $parent->id]);

        // Crée une collection vide pour stocker les ascendants
        $ascendants = collect();

        // Appelle la méthode UtilService::get_ascendants
        $result = UtilService::get_ascendants($child, $ascendants);

        // Vérifie que le résultat contient les ascendants corrects
        $this->assertCount(2, $result);

        // Vérifie que les identifiants des zones sont bien présents
        $this->assertTrue($result->pluck('id')->contains($parent->id));
        $this->assertTrue($result->pluck('id')->contains($grandParent->id));
    }

    /** @test */
    public function it_returns_empty_if_zone_has_no_parents()
    {
        // Crée une zone sans parent
        $zone = Zone::factory()->create(['name' => 'Root Zone']);

        // Crée une collection vide pour stocker les ascendants
        $ascendants = collect();

        // Appelle la méthode UtilService::get_ascendants
        $result = UtilService::get_ascendants($zone, $ascendants);

        // Vérifie que la collection est vide
        $this->assertEmpty($result);
    }
}
