<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class BuyerController extends Controller
{
    // Show all buyers
    public function index()
    {
        return view('admin.buyers.index');
    }

    // Fetch buyers for DataTable
    public function getBuyers(Request $request)
    {
        $buyers = User::where('role', 'buyer')
            ->when($request->name, function($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($request->email, function($query, $email) {
                $query->where('email', 'like', "%{$email}%");
            })
            ->when($request->status !== null, function($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('name', 'asc');

        return DataTables::of($buyers)
            ->addIndexColumn()
            ->editColumn('status', function($buyer) {
                $status = $buyer->status ? 'active' : 'inactive';
                $class = $buyer->status ? 'badge bg-success' : 'badge bg-danger';
                return '<span class="'.$class.'">'.ucfirst($status).'</span>';
            })
            ->editColumn('created_at', function($buyer) {
                return $buyer->created_at->format('d M Y');
            })
            ->addColumn('action', function($buyer) {
                return '
                    <a href="'.route('admin.buyers.edit', $buyer->id).'" class="btn btn-sm btn-soft-primary">
                        <i class="bi bi-pencil"></i> Edit
                    </a>
                    <button class="btn btn-sm btn-soft-danger delete-buyer" data-id="'.$buyer->id.'">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    // Show create buyer form
    public function create()
    {
        return view('admin.buyers.create');
    }

    // Store new buyer
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:15',
            'password' => 'required|string|min:8',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $buyer = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => 'buyer',
                'password' => bcrypt($request->password),
                'status' => $request->status
            ]);

            return redirect()
                ->route('admin.buyers.index')
                ->with('success', 'Buyer created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create buyer: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show edit buyer form
    public function edit($id)
    {
        $buyer = User::where('id', $id)
                    ->where('role', 'buyer')
                    ->firstOrFail();

        return view('admin.buyers.edit', compact('buyer'));
    }

    // Update buyer
    public function update(Request $request, $id)
    {
        $buyer = User::where('id', $id)
                    ->where('role', 'buyer')
                    ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($buyer->id)
            ],
            'phone' => 'nullable|string|max:15',
            'password' => 'nullable|string|min:8',
            'status' => 'required|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $data = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status
            ];

            if ($request->filled('password')) {
                $data['password'] = bcrypt($request->password);
            }

            $buyer->update($data);

            return redirect()
                ->route('admin.buyers.index')
                ->with('success', 'Buyer updated successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to update buyer: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Delete buyer
    public function destroy($id)
    {
        $buyer = User::where('id', $id)
                    ->where('role', 'buyer')
                    ->firstOrFail();

        try {
            $buyer->delete();

            return response()->json([
                'success' => true,
                'message' => 'Buyer deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete buyer: ' . $e->getMessage()
            ], 500);
        }
    }
}