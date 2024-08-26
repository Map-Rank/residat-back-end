<?php

namespace Tests\Feature;

use Mockery;
use Tests\TestCase;
use App\Models\User;
use App\Models\Zone;
use App\Models\Level;
use App\Models\Report;
use App\Models\Vector;
use App\Models\VectorKey;
use App\Models\MetricType;
use App\Models\ReportItem;
use Illuminate\Support\Str;
use App\Service\UtilService;
use Laravel\Sanctum\Sanctum;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ReportControllerTest extends TestCase
{
    use RefreshDatabase;

    // public function setUp(): void
    // {
    //     parent::setUp();
    //     // $this->seed();
    //     Sanctum::actingAs(
    //         User::first()
    //     );
    // }

    public function test_index_displays_reports()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
            // dd($user);
        }
        
        $this->actingAs($user);
        // Crée quelques rapports
        Report::factory()->count(3)->create();

        // Simule la requête GET à la route index
        $response = $this->get(route('reports.index'));

        // Vérifie que la réponse a un statut 200
        $response->assertStatus(200);

        // Vérifie que les rapports sont passés à la vue
        $response->assertViewHas('reports');
    }

    public function test_create_resource_completion_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('ressource.completion.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.ressource-completion');
    }

    public function test_create_fishing_vulnerability_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('fishing.vulnerability.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.fishing-vulnerability');
    }

    public function test_create_water_stress_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('water.stress.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.water-stress');
    }

    public function test_create_migration_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('migration.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.migration');
    }

    public function test_create_health_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('health-report-items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.health-create');
    }

    public function test_create_agriculture_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('agriculture.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.agriculture-create');
    }

    public function test_create_infrastructure_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('infrastructure.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.infrastructure-create');
    }

    public function test_create_social_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('social.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.social-create');
    }

    public function test_create_select_security_displays_zones()
    {
        // Créer ou obtenir un utilisateur
        $user = User::first();
        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);

        // Supprimer toutes les zones existantes pour garantir que la table est vide
        Zone::query()->delete();

        // Créer les niveaux nécessaires
        $levels = [
            Level::create(['name' => 'Country']),
            Level::create(['name' => 'Region']),
            Level::create(['name' => 'Division']),
            Level::create(['name' => 'SubDivision']),
        ];

        // Assumer que nous utilisons le dernier niveau créé pour les zones
        $level4 = $levels[3]; // Par exemple, le niveau 'SubDivision'

        // Créer 10 zones avec le level_id spécifié
        $zones = [];
        foreach (range(1, 10) as $index) {
            $zones[] = Zone::create([
                'name' => 'Test Zone ' . $index,
                'level_id' => $level4->id,
            ]);
        }

        // Simuler la requête GET à la route de création de rapport ressource completion
        $response = $this->get(route('food.security.report.items.create'));

        // Vérifier que la vue correcte est retournée avec les zones
        $response->assertStatus(200);
        $response->assertViewIs('reports.food-security');
    }

    // public function test_store_creates_report_with_image_and_items()
    // {
    //     $user = User::first();

    //     if (!$user) {
    //         $user = User::factory()->admin()->create();
    //     }
        
    //     $this->actingAs($user);

    //     // Simule les données de la requête
    //     $data = Report::factory()->make()->toArray();
    //     $data['vector_keys'] = [
    //         ['value' => 'key1', 'type' => 'type1', 'name' => 'name1'],
    //         ['value' => 'key2', 'type' => 'type2', 'name' => 'name2']
    //     ];
    //     $data['report_items'] = [
    //         ['metric_type_id' => 1, 'value' => 100],
    //         ['metric_type_id' => 2, 'value' => 200]
    //     ];

    //     // Simule le fichier image dans la requête
    //     $file = UploadedFile::fake()->image('report_image.jpg');
    //     $data['image'] = $file;

    //     // Simule la requête POST à la route store
    //     $response = $this->post(route('reports.store'), $data);

    //     // Vérifie que la redirection est correcte
    //     $response->assertRedirect(route('reports.index'));

    //     // Vérifie que le rapport a été créé dans la base de données
    //     $this->assertDatabaseHas('reports', [
    //         'user_id' => $user->id,
    //         'description' => $data['description']
    //     ]);

    //     // Vérifie que le fichier a été uploadé sur le disque
    //     Storage::disk('s3')->assertExists('report_images/' . $file->hashName());

    //     // Vérifie que les report_items ont été créés
    //     foreach ($data['report_items'] as $item) {
    //         $this->assertDatabaseHas('report_items', $item);
    //     }

    //     // Vérifie que les vector_keys ont été créées
    //     foreach ($data['vector_keys'] as $key) {
    //         $this->assertDatabaseHas('vector_keys', $key);
    //     }
    // }

    public function test_destroy_deletes_report()
    {
        $user = User::first();

        if (!$user) {
            $user = User::factory()->admin()->create();
        }
        
        $this->actingAs($user);
        // Crée un rapport
        $report = Report::factory()->create();

        // Simule la requête DELETE à la route destroy
        $response = $this->delete(route('reports.destroy', $report->id));

        // Vérifie que la redirection est correcte
        $response->assertRedirect();

        // Vérifie que le rapport a été supprimé
        $this->assertSoftDeleted('reports', ['id' => $report->id]);
    }
}