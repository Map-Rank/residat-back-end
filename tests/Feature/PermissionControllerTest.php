<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionSeeder;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllRolesWithPermissions()
    {
        // Insérez ici le code pour exécuter votre seeder si ce n'est pas déjà fait
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);

        // Vérification des rôles avec leurs permissions
        $admin = Role::findByName('admin');
        $default = Role::findByName('default');

        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $this->assertNotNull($admin);
        $this->assertNotNull($default);

        // Insérez ici des assertions pour vérifier que chaque rôle a les permissions attendues
        // Par exemple:
        $this->assertTrue($admin->hasPermissionTo('interaction-list'));
        $this->assertTrue($admin->hasPermissionTo('interaction-create'));
        // ...

        $this->assertTrue($default->hasPermissionTo('user-list'));
        $this->assertTrue($default->hasPermissionTo('user-create'));
        // ...

        // Vérification du chargement de la vue
        $response = $this->get('/permissions');

        $response->assertStatus(200);
        $response->assertViewIs('permissions.index');
    }

    public function testGetAllUsersWithRolesAndPermissions()
    {
        $response = $this->get('/your_route_to_get_all_users_with_roles_and_permissions');

        $response->assertStatus(200);
    }

    // public function testShowRole()
    // {
    //     $role = Role::factory()->create();

    //     $response = $this->get("/your_route_to_show_role/{$role->id}");

    //     $response->assertStatus(200);
    // }

    // public function testUpdatePermissions()
    // {
    //     $role = Role::factory()->create();
    //     $user = User::factory()->create();
    //     $permissions = []; // Specify your permissions array here

    //     $response = $this->post("/your_route_to_update_permissions/{$role->id}", [
    //         'permissions' => $permissions,
    //     ]);

    //     $response->assertStatus(200); // Assuming you return a view with HTTP 200
    // }
}