<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index()
    {
        
        return response()->json(User::with('roles')->get());
    }

    public function store(Request $request)
    {
        
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'is_active' => $data['is_active'] ?? true,
        ]);

        $role = Role::findOrFail($data['role_id']);
        $user->assignRole($role);

        return response()->json(['message' => 'User created', 'data' => $user->load('roles')], 201);
    }

    public function show(User $user)
    {
        
        return response()->json($user->load('roles'));
    }

    public function update(Request $request, User $user)
    {
        
        $data = $request->validate([
            'name' => 'string|max:255',
            'email' => 'email|unique:users,email,' . $user->id,
            'role_id' => 'exists:roles,id',
            'is_active' => 'boolean',
        ]);

        $user->update($request->only(['name', 'email', 'is_active']));

        if ($request->has('role_id')) {
            $role = Role::findOrFail($data['role_id']);
            $user->syncRoles([$role]);
        }

        return response()->json(['message' => 'User updated', 'data' => $user->load('roles')]);
    }

    public function destroy(User $user)
    {
        
        $user->delete();
        return response()->json(['message' => 'User deleted']);
    }
}