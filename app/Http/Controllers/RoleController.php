<?php
namespace App\Http\Controllers;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        
        return response()->json(Role::with('permissions')->get());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'string|nullable',
            'permission_ids' => 'array|nullable',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'description' => $data['description'],
        ]);

        if ($request->permission_ids) {
            $role->syncPermissions($data['permission_ids']);
        }

        return response()->json(['message' => 'Role created', 'data' => $role->load('permissions')], 201);
    }

    public function show(Role $role)
    {
        
        return response()->json($role->load('permissions'));
    }

    public function update(Request $request, Role $role)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255|unique:roles,name,' . $role->id,
            'description' => 'string|nullable',
            'permission_ids' => 'array|nullable',
            'permission_ids.*' => 'exists:permissions,id',
        ]);

        $role->update($request->only(['name', 'description']));

        if ($request->has('permission_ids')) {
            $role->syncPermissions($data['permission_ids']);
        }

        return response()->json(['message' => 'Role updated', 'data' => $role->load('permissions')]);
    }

    public function destroy(Role $role)
    {
        
        $role->delete();
        return response()->json(['message' => 'Role deleted']);
    }
}