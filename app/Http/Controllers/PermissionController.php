<?php
namespace App\Http\Controllers;

use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index()
    {
        
        return response()->json(Permission::all());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:permissions,name',
            'description' => 'string|nullable',
        ]);

        $permission = Permission::create($data);
        return response()->json(['message' => 'Permission created', 'data' => $permission], 201);
    }

    public function show(Permission $permission)
    {
        
        return response()->json($permission);
    }

    public function update(Request $request, Permission $permission)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255|unique:permissions,name,' . $permission->id,
            'description' => 'string|nullable',
        ]);

        $permission->update($data);
        return response()->json(['message' => 'Permission updated', 'data' => $permission]);
    }

    public function destroy(Permission $permission)
    {
        
        $permission->delete();
        return response()->json(['message' => 'Permission deleted']);
    }
}