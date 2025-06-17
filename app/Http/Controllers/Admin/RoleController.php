<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::with('permissions')->get();
        return response()->json($roles);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:roles,id',
            'permissions' => 'array',
        ]);

        $role = Role::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            foreach ($data['permissions'] as $module => $perms) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'module' => $module,
                    'can_add' => in_array('add', $perms),
                    'can_edit' => in_array('edit', $perms),
                    'can_view' => in_array('view', $perms),
                    'can_export' => in_array('export', $perms),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:roles,id',
            'permissions' => 'array',
        ]);

        $role->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->delete();
            foreach ($data['permissions'] as $module => $perms) {
                RolePermission::create([
                    'role_id' => $role->id,
                    'module' => $module,
                    'can_add' => in_array('add', $perms),
                    'can_edit' => in_array('edit', $perms),
                    'can_view' => in_array('view', $perms),
                    'can_export' => in_array('export', $perms),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
