<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\RolePermission;
use App\Models\Module;
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
                $permBtn = '<a href="'.route('admin.roles.permissions', $role->id).'" class="btn btn-soft-secondary btn-sm me-1"><i class="bi bi-lock"></i></a>';
                $editBtn = '<a href="'.route('admin.roles.edit', $role->id).'" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a>';
                return $permBtn.$editBtn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    // Show create role form
    public function create()
    {
        $roles = Role::orderBy('name', 'asc')->get();
        $modules = Module::where('status', '1')->orderBy('name')->get();
        $actions = ['add', 'edit', 'view', 'delete'];
        return view('admin.roles.create', compact('roles', 'modules', 'actions'));
    }

    // Show edit role form
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $roles = Role::where('id', '!=', $id)->orderBy('name', 'asc')->get();
        $modules = Module::where('status', '1')->orderBy('name')->get();
        $actions = ['add', 'edit', 'view', 'delete'];
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
        $modules = Module::pluck('id')->toArray();

        $role = Role::create([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if (!empty($data['permissions'])) {
            foreach ($data['permissions'] as $moduleId => $perms) {
                if (!in_array($moduleId, $modules)) {
                    continue;
                }
                RolePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_add' => in_array('add', $perms),
                    'can_edit' => in_array('edit', $perms),
                    'can_view' => in_array('view', $perms),
                    'can_delete' => in_array('delete', $perms),
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
        $modules = Module::pluck('id')->toArray();

        $role->update([
            'name' => $data['name'],
            'parent_id' => $data['parent_id'] ?? null,
        ]);

        if (isset($data['permissions'])) {
            $role->permissions()->delete();
            foreach ($data['permissions'] as $moduleId => $perms) {
                if (!in_array($moduleId, $modules)) {
                    continue;
                }
                RolePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_add' => in_array('add', $perms),
                    'can_edit' => in_array('edit', $perms),
                    'can_view' => in_array('view', $perms),
                    'can_delete' => in_array('delete', $perms),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }

    public function permissions($id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $modules = Module::where('status', '1')->orderBy('name')->get();
        $actions = ['add', 'edit', 'view', 'delete'];
        return view('admin.roles.permissions', compact('role', 'modules', 'actions'));
    }

    public function updatePermissions(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $data = $request->validate([
            'permissions' => 'array',
            'permissions.*' => 'array',
        ]);

        $modules = Module::pluck('id')->toArray();

        $role->permissions()->delete();

        if (!empty($data['permissions'])) {
            foreach ($data['permissions'] as $moduleId => $perms) {
                if (!in_array($moduleId, $modules)) {
                    continue;
                }
                RolePermission::create([
                    'role_id' => $role->id,
                    'module_id' => $moduleId,
                    'can_add' => in_array('add', $perms),
                    'can_edit' => in_array('edit', $perms),
                    'can_view' => in_array('view', $perms),
                    'can_delete' => in_array('delete', $perms),
                ]);
            }
        }

        return response()->json(['status' => 'success']);
    }
}
