<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;

/**
 * @group Module Permissions
 */
class PermissionController extends Controller
{
    public function getAllRolesWithPermissions()
    {
        $roles = Role::with('permissions')->get();
        $permissions = Permission::all();

        return view('permissions.index',[
            'roles' => $roles,
            'permissions' => $permissions,
        ]);
    }

    /**
     * Store role.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required','string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please check the fields.');
        }

        $validated = $validator->validated();
        $datum = Role::query()->where('name', $validated['name'])->first();

        if ($datum != null){
            return redirect()->back()->with('error', 'This role name already exists.');
        }

        $validated['guard_name'] = 'web';
        $role = new Role($validated);
        if (!$role->save())
        {
            redirect()->back()->with('error','An error has occurred');
        }

        $role->syncPermissions($request->input('permissions'));

        return redirect()->back()->with('success', 'Rôle created successfully');
    }

    /**
     * Update role.
     *
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please check the fields.');
        }

        $validated = $validator->validated();
        $datum = Role::query()->where('name', $validated['name'])->where('id', '!=', $id)->first();

        if ($datum != null) {
            return redirect()->back()->with('error', 'This role name already exists.');
        }

        $role = Role::findOrFail($id);
        $role->name = $validated['name']; // Mise à jour du nom du rôle

        if (!$role->save()) {
            return redirect()->back()->with('error', 'An error has occurred');
        }

        $role->syncPermissions($request->input('permissions')); // Mise à jour des permissions du rôle

        return redirect()->back()->with('success', 'Rôle updated successfully');
    }

    /**
     * @codeCoverageIgnore
     */
    public function getAllUsersWithRolesAndPermissions()
    {
        $users = User::with(['roles.permissions'])->get();

        return view('permissions.lists',[
            'users' => $users
        ]);
    }
    /**
     * @codeCoverageIgnore
     */
    public function showRole($id)
    {
        $role = Role::find($id);

        if (!$role) {
            return abort(404); // Ou une autre gestion d'erreur appropriée
        }

        $permissions = Permission::all();

        return view('permissions.show', compact('role', 'permissions'));
    }

    /**
     * @codeCoverageIgnore
     */
    public function updatePermissions(Request $request, $id)
    {
        $role = Role::where('guard_name','web')->where('id',$id)->first();

        if (!$role) {
            return abort(404); // Ou une autre gestion d'erreur appropriée
        }

        $role->syncPermissions($request->input('permissions', []));

        $permissions = Permission::all();

        return view('permissions.show', compact('role', 'permissions'));

        // return redirect()->route('permissions.show', ['id' => $role->id])->with('success', 'Role permissions updated successfully.');
    }

    public function storePermission(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please check the fields.');
        }

        $permission = Permission::create([
            'name' => $request->input('name'),
            'guard_name' => 'web',
        ]);

        return redirect()->back()->with('success', 'Permission created successfully');
    }

    public function getAllPermissions()
    {
        $permissions = Permission::paginate(15);

        return view('permissions.permissions',[
            'permissions' => $permissions,
        ]);
    }

    public function updateUniqPermission(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:permissions,name,' . $id,
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please check the fields.');
        }

        $permission = Permission::findOrFail($id);

        $permission->update([
            'name' => $request->input('name'),
        ]);

        return redirect()->back()->with('success', 'Permission updated successfully.');
    }

    public function deletePermission($id)
    {
        $permission = Permission::findOrFail($id);

        $permission->delete();

        return redirect()->back()->with('success', 'Permission deleted successfully.');
    }
}