<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use App\Service\UtilService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UtilServiceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        // Crée des niveaux nécessaires pour les tests
        Level::factory()->create(['id' => 4, 'name' => 'Level 4']);
        Level::factory()->create(['id' => 3, 'name' => 'Level 3']);
        Level::factory()->create(['id' => 5, 'name' => 'Level 5']);
    }

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

    /** @test */
    public function it_returns_zones_with_level_id_4_for_user_with_valid_zone_and_descendants()
    {
        // Crée une zone avec des descendants ayant level_id 4
        $zone = Zone::factory()->create(['name' => 'Parent Zone', 'level_id' => 3]);
        $child1 = Zone::factory()->create(['name' => 'Child Zone 1', 'parent_id' => $zone->id, 'level_id' => 4]);
        $child2 = Zone::factory()->create(['name' => 'Child Zone 2', 'parent_id' => $zone->id, 'level_id' => 4]);

        // Crée un utilisateur avec une zone_id valide
        $user = User::factory()->create(['email' => 'newusers@example.com','zone_id' => $zone->id]);

        // Appelle la méthode
        $result = UtilService::getZonesWithLevelId4ForUser($user);

        // Vérifie que le résultat contient les zones ayant level_id 4
        $this->assertCount(2, $result);
        $this->assertTrue($result->pluck('id')->contains($child1->id));
        $this->assertTrue($result->pluck('id')->contains($child2->id));
    }

    /** @test */
    public function it_returns_empty_if_no_descendants_with_level_id_4()
    {
        // Crée une zone avec des descendants n'ayant pas level_id 4
        $zone = Zone::factory()->create(['name' => 'Parent Zone', 'level_id' => 3]);
        $child1 = Zone::factory()->create(['name' => 'Child Zone 1', 'parent_id' => $zone->id, 'level_id' => 3]);
        $child2 = Zone::factory()->create(['name' => 'Child Zone 2', 'parent_id' => $zone->id, 'level_id' => 5]);

        // Crée un utilisateur avec une zone_id valide
        $user = User::factory()->create(['email' => 'newusers@example.com','zone_id' => $zone->id]);

        // Appelle la méthode
        $result = UtilService::getZonesWithLevelId4ForUser($user);

        // Vérifie que le résultat est vide
        $this->assertEmpty($result);
    }

    /** @test */
    public function it_returns_empty_if_zone_does_not_exist()
    {
        // Crée une zone fictive pour assigner l'utilisateur
        $zone = Zone::factory()->create(['name' => 'Temporary Zone', 'level_id' => 3]);

        // Crée un utilisateur avec une zone_id valide
        $user = User::factory()->create(['email' => 'newusers@example.com','zone_id' => $zone->id]);

        // Supprime la zone pour simuler une zone inexistante
        $zone->delete();

        // Appelle la méthode
        $result = UtilService::getZonesWithLevelId4ForUser($user);

        // Vérifie que le résultat est vide
        $this->assertEmpty($result);
    }
}
