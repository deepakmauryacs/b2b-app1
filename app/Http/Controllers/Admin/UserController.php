<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        return view('admin.users.index');
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'role' => ['required', Rule::in(["vendor", "buyer", "admin"])],
            'password' => 'required|string|min:8',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        $data['password'] = bcrypt($data['password']);

        try {
            User::create($data);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'User created successfully!',
                    'redirect' => route('admin.users.index'),
                ]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to create user: ' . $e->getMessage(),
                ], 500);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User created successfully!');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:15',
            'role' => ['required', Rule::in(["vendor", "buyer", "admin"])],
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();

        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {
            $user->update($data);

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'User updated successfully!',
                    'redirect' => route('admin.users.index'),
                ]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to update user: ' . $e->getMessage(),
                ], 500);
            }
        }

        return redirect()->route('admin.users.index')
            ->with('success', 'User updated successfully!');
    }

    public function renderUsersTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $query = User::query()
            ->when($request->name, function ($q, $name) {
                $q->where('name', 'like', "{$name}%");
            })
            ->when($request->email, function ($q, $email) {
                $q->where('email', $email);
            })
            ->when($request->role, function ($q, $role) {
                $q->where('role', $role);
            })
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->orderBy('name', 'asc');

        $users = $query->paginate($perPage, ['*'], 'page', $page);

        return view('admin.users._users_table', compact('users'));
    }
}
