<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Spatie\Permission\Models\Role;
use Database\Seeders\PermissionSeeder;
use Spatie\Permission\Models\Permission;
use Database\Factories\PermissionFactory;
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
        // Insérez ici le code pour exécuter votre seeder si ce n'est pas déjà fait
        $this->seed(RoleSeeder::class);
        $this->seed(PermissionSeeder::class);
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $response = $this->get('/roles');

        $response->assertStatus(200);
    }

    public function testStoreRole()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $response = $this->post('/create-role', [
            'name' => 'newRole',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['name' => 'newRole']);
    }

    public function testStoreRoleValidationFails()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $response = $this->post('/create-role', [
            'name' => '',
        ]);

        $response->assertSessionHas('error', 'Please check the fields.');
    }

    public function testUpdateRole()
    {
        // Créer un utilisateur et le connecter
        $user = User::factory()->create();
        $this->actingAs($user);

        // Créer un rôle
        $response = $this->post('/create-role', [
            'name' => 'existingRole',
        ]);

        // Vérifiez que le rôle a été créé
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Rôle created successfully');

        // Récupérer le rôle créé
        $role = Role::where('name', 'existingRole')->first();

        // Mettre à jour le rôle
        $updateResponse = $this->put("/update-role/{$role->id}", [
            'name' => 'updatedRole',
        ]);

        // Vérifiez que le rôle a été mis à jour
        $updateResponse->assertRedirect();
        $updateResponse->assertSessionHas('success', 'Rôle updated successfully');

        // Vérifiez que la base de données contient le rôle mis à jour
        $this->assertDatabaseHas('roles', ['name' => 'updatedRole']);
    }

    public function testUpdateRoleValidationFails()
    {
        // Créer un utilisateur et le connecter
        $user = User::factory()->create();
        $this->actingAs($user);

        // Créer un rôle
        $response = $this->post('/create-role', [
            'name' => 'existingRole',
        ]);

        // Vérifiez que le rôle a été créé
        $response->assertRedirect();
        $response->assertSessionHas('success', 'Rôle created successfully');

        // Récupérer le rôle créé
        $role = Role::where('name', 'existingRole')->first();

        // Mettre à jour le rôle
        $updateResponse = $this->put("/update-role/{$role->id}", [
            'name' => '',
        ]);

        $response->assertSessionHas('error', 'Please check the fields.');
    }

    public function testStorePermission()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        
        $response = $this->post('/create-permissions', [
            'name' => 'newPermission',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', ['name' => 'newPermission']);
    }

    public function testStorePermissionValidationFails()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable
        $response = $this->post('/create-permissions', [
            'name' => '',
        ]);

        $response->assertSessionHas('error', 'Please check the fields.');
    }

    public function testGetAllPermissions()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        PermissionFactory::new()->count(20)->create();

        $response = $this->get('/all-permissions');

        $response->assertStatus(200);
        $response->assertViewHas('permissions');
    }

    public function testUpdateUniqPermission()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $permission = PermissionFactory::new()->create(['name' => 'existingPermission']);

        $response = $this->put("/update-permissions/{$permission->id}", [
            'name' => 'updatedPermission',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('permissions', ['name' => 'updatedPermission']);
    }

    public function testUpdateUniqPermissionValidationFails()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $permission = PermissionFactory::new()->create(['name' => 'existingPermission']);

        $response = $this->put("/update-permissions/{$permission->id}", [
            'name' => '',
        ]);

        $response->assertSessionHas('error', 'Please check the fields.');
    }

    public function testDeletePermission()
    {
        $user = User::first();

        // Si aucun utilisateur n'existe, créez-en un
        if (!$user) {
            $user = User::factory()->create();
        }

        $this->actingAs($user); // Authenticate if applicable

        $permission = PermissionFactory::new()->create(['name' => 'existingPermission']);

        $response = $this->delete("/delete-permission/{$permission->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('permissions', ['id' => $permission->id]);
    }


}