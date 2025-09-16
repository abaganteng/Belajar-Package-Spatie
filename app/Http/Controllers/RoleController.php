<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Data\ManageUserData;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function view()
    {
        return inertia('role/view');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
        ]);

        Role::create([
            'name' => $request->name,
            'guard_name' => 'web'
        ]);

        flash('New role has been successfully created.');

        return back();
        
    }

    public function assignRole()
    {
        $users = User::select(['id', 'name'])->get();
        $roles = Role::select(['id', 'name'])->get();
        return inertia('role/assign-role', [
            'users' => fn () => ManageUserData::collect($users),
            'roles' => fn () => ManageUserData::collect($roles),
        ]);
    }

    public function storeAssignRole(Request $request)
    {
        $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_id' => 'required|integer|exists:roles,id',
        ]);

        $user = User::findOrFail($request->user_id);
        $role = Role::findOrFail($request->role_id);

        $user->assignRole($role->name);

        flash("Role {$role->name} has been successfully assigned to {$user->name}.");

        return back();
    }
}
