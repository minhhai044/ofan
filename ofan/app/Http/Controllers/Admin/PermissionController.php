<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->latest('id')->get();
        return view('admin.permissions.index', compact('roles'));
    }

    public function edit(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        $rolePermissions = $role->permissions->pluck('name')->toArray();
        return view('admin.permissions.edit', compact('role', 'permissions', 'rolePermissions'));
    }
    public function update(Request $request, string $id)
    {
        $role = Role::findOrFail($id);

        $selected = $request->input('permissions', []);
        $role->syncPermissions($selected);

        app(\Spatie\Permission\PermissionRegistrar::class)->forgetCachedPermissions();

        return back()->with('success', 'Cập nhật quyền thành công!');
    }
}
