<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

/**
 * @group Module Permissions
 */
class PermissionController extends Controller
{
    public function getAllRolesWithPermissions()
    {
        $roles = Role::with('permissions')->get();

        return view('permissions.index',[
            'roles' => $roles
        ]);
    }

    public function getAllUsersWithRolesAndPermissions()
    {
        $users = User::with(['roles.permissions'])->get();

        return view('permissions.lists',[
            'users' => $users
        ]);
    }

    public function showRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return abort(404); // Ou une autre gestion d'erreur appropriée
        }

        $permissions = Permission::all();

        return view('permissions.show', compact('role', 'permissions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::where('guard_name','web')->where('id',$id)->first();

        if (!$role) {
            return abort(404); // Ou une autre gestion d'erreur appropriée
        }

        $role->syncPermissions($request->input('permissions', []));

        return redirect()->route('permissions.show', ['id' => $role->id])->with('success', 'Role permissions updated successfully.');
    }
}