<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PermissionControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testGetAllRolesWithPermissions()
    {
        $response = $this->get('/your_route_to_get_all_roles_with_permissions');

        $response->assertStatus(200);
    }

    public function testGetAllUsersWithRolesAndPermissions()
    {
        $response = $this->get('/your_route_to_get_all_users_with_roles_and_permissions');

        $response->assertStatus(200);
    }

    public function testShowRole()
    {
        $role = Role::factory()->create();

        $response = $this->get("/your_route_to_show_role/{$role->id}");

        $response->assertStatus(200);
    }

    public function testUpdatePermissions()
    {
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $permissions = []; // Specify your permissions array here

        $response = $this->post("/your_route_to_update_permissions/{$role->id}", [
            'permissions' => $permissions,
        ]);

        $response->assertStatus(200); // Assuming you return a view with HTTP 200
    }
}