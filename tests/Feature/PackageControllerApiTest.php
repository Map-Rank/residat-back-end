<?php

namespace Tests\Feature;

// use IlluminateFoundationTestingRefreshDatabase;
use Tests\TestCase;
use App\Models\Package;
use App\Models\User;
use App\Http\Resources\PackageResource;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;

class PackageControllerApiTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test index method (list all active packages)
     */
    public function test_packages_index()
    {
        Package::factory()->create(['is_active' => true]);
        Package::factory()->create(['is_active' => false]);

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        $response = $this->getJson(route('packages.index'));
        $response->assertStatus(200);
        $response->assertJsonCount(1, 'data'); 
    }

    /**
     * Test show method (show a specific package)
     */
    public function test_packages_show_success()
    {
        $package = Package::factory()->create(['is_active' => true]);

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }
        
        $this->actingAs($user);

        $response = $this->getJson(route('packages.show', $package->id));
        $response->assertStatus(200);
        $response->assertJsonFragment([
            'name_fr' => $package->name_fr,
            'name_en' => $package->name_en,
        ]);
    }

    /**
     * Test show method (package not found)
     */
    public function test_packages_show_not_found()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user);

        $response = $this->getJson(route('packages.show', 9999)); 
        $response->assertStatus(404);
        $response->assertJsonFragment(['message' => __('ID not found')]);
    }

    /**
     * Test store method (create a new package)
     */
    public function test_packages_store()
    {
        $data = [
            'name_fr' => 'Package test',
            'name_en' => 'Test Package',
            'level' => 'National',
            'price' => 100,
            'periodicity' => 'Month',
            'description_fr' => 'Description en français',
            'description_en' => 'Description in English',
            'is_active' => true,
        ];

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        $response = $this->postJson(route('packages.store'), $data);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name_fr' => 'Package test']);
    }

    /**
     * Test update method (update a package)
     */
    public function test_packages_update()
    {
        $package = Package::factory()->create();
        
        $data = [
            'name_fr' => 'Updated package',
            'name_en' => 'Updated Package',
            'level' => 'Regional',
            'price' => 200,
            'periodicity' => 'Quarter',
            'description_fr' => 'Updated description en français',
            'description_en' => 'Updated description in English',
            'is_active' => true,
        ];

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        $response = $this->putJson(route('packages.update', $package->id), $data);
        $response->assertStatus(200);
        $response->assertJsonFragment(['name_fr' => 'Updated package']);
    }

    public function test_package_update_not_found()
    {
        // Créez un utilisateur pour l'authentification
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }

        $this->actingAs($user);

        // Créez des données de mise à jour pour le package
        $data = [
            'name_fr' => 'Updated package',
            'name_en' => 'Updated Package',
            'level' => 'Regional',
            'price' => 200,
            'periodicity' => 'Quarter',
            'description_fr' => 'Updated description en français',
            'description_en' => 'Updated description in English',
            'is_active' => true,
        ];

        // Supposons qu'il n'y ait aucun package avec cet ID
        $nonExistentPackageId = 999; // Un ID qui ne correspond à aucun package

        // Effectuer la requête PUT pour mettre à jour un package qui n'existe pas
        $response = $this->putJson(route('packages.update', $nonExistentPackageId), $data);

        // Vérifier que la réponse est bien une erreur 404
        $response->assertStatus(404);

        // Vérifier que le message d'erreur est bien celui attendu dans la structure correcte
        $response->assertJsonFragment([
            'message' => 'Package not found'
        ]);

        // Vérifiez également que le statut est bien 'false' comme défini dans la macro
        $response->assertJsonFragment([
            'status' => false
        ]);
    }

    /**
     * Test destroy method (delete a package)
     */
    public function test_packages_destroy()
    {
        $package = Package::factory()->create();

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        $response = $this->deleteJson(route('packages.destroy', $package->id));
        $response->assertStatus(200);
        $response->assertJson(['message' => __('Package deleted successfully')]);

        $this->assertDatabaseMissing('packages', ['id' => $package->id]);
    }
}
