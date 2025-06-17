<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use Yajra\DataTables\DataTables;

class RoleController extends Controller
{
    // Show role list page
    public function index()
    {
        return view('admin.roles.index');
    }

    /**
     * Render roles table similar to vendors/users list for AJAX pagination.
     */
    public function renderRolesTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $query = Role::query()
            ->with('parent')
            ->when($request->name, function ($q, $name) {
                $q->where('name', 'like', "{$name}%");
            })
            ->orderBy('name', 'asc');

        $roles = $query->paginate($perPage, ['*'], 'page', $page);

        return view('admin.roles._roles_table', compact('roles'));
    }

    // Fetch roles for DataTable
    public function getRoles()
    {
        $roles = Role::with('parent')->orderBy('name', 'asc');
        return DataTables::of($roles)
            ->addIndexColumn()
            ->addColumn('parent', function ($role) {
                return $role->parent ? $role->parent->name : '-';
            })
            ->addColumn('action', function ($role) {
                return '<a href="'.route('admin.roles.edit', $role->id).'" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Show create role form
    public function create()
    {
        $roles = Role::orderBy('name', 'asc')->get();
        $modules = config('modules');
        $actions = ['add', 'edit', 'view', 'export'];
        return view('admin.roles.create', compact('roles', 'modules', 'actions'));
    }

    // Show edit role form
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $roles = Role::where('id', '!=', $id)->orderBy('name', 'asc')->get();
        $modules = config('modules');
        $actions = ['add', 'edit', 'view', 'export'];
        return view('admin.roles.edit', compact('role', 'roles', 'modules', 'actions'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'parent_id' => 'nullable|exists:roles,id',
            'permissions' => 'array',
            'permissions.*' => 'array',
        ]);
        $modules = config('modules');

        $role = Role::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            foreach ($data['permissions'] as $module => $perms) {
                if (!in_array($module, $modules)) {
                    continue;
                }
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
            'permissions.*' => 'array',
        ]);
        $modules = config('modules');

        $role->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->delete();
            foreach ($data['permissions'] as $module => $perms) {
                if (!in_array($module, $modules)) {
                    continue;
                }
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
