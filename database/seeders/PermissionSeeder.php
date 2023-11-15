<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'interaction-list', 'interaction-create', 'interaction-edit', 'interaction-delete',
            'level-list', 'level-create', 'level-edit', 'level-delete',
            'media-list', 'media-create', 'media-edit', 'media-delete',
            'organization-list', 'organization-create', 'organization-edit', 'organization-delete',
            'post-list', 'post-create', 'post-edit', 'post-delete',
            'subscription-list', 'subscription-create', 'subscription-edit', 'subscription-delete',
            'typeInteraction-list', 'typeInteraction-create', 'typeInteraction-edit', 'typeInteraction-delete',
            'permission-list', 'permission-create', 'permission-edit', 'permission-delete',
            'role-list', 'role-create', 'role-edit', 'role-delete',
            'zone-list', 'zone-create', 'zone-edit', 'zone-delete',
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'userSubscription-list', 'userSubscription-create', 'userSubscription-edit', 'userSubscription-delete',
        ];

        $defaultPermissions = [
            'interaction-list',
            'level-list',
            'media-list', 'media-create',
            'organization-list',
            'post-list', 'post-create', 'post-edit', 'post-delete',
            'subscription-list',
            'typeInteraction-list',
            'permission-list',
            'role-list',
            'zone-list',
            'user-list', 'user-create', 'user-edit', 'user-delete',
            'userSubscription-list', 'userSubscription-create', 'userSubscription-edit', 'userSubscription-delete',
        ];


       foreach ($permissions as $permission)
       { Permission::query()->updateOrCreate(['name' => $permission]); }

       $admin = Role::findByName('admin');
       $admin->syncPermissions($permissions);

       $default = Role::findByName('default');
       $default->syncPermissions($defaultPermissions);

    }
}
