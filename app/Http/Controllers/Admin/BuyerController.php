<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

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
            ->with('buyerProfile')
            ->when($request->name, function($query, $name) {
                $query->where('name', 'like', "%{$name}%");
            })
            ->when($request->email, function($query, $email) {
                $query->where('email', 'like', "%{$email}%");
            })
            ->when($request->phone, function($query, $phone) {
                $query->where('phone', $phone);
            })
            ->when($request->gst_no, function($query, $gstNo) {
                $query->whereHas('buyerProfile', function($q2) use ($gstNo) {
                    $q2->where('gst_no', $gstNo);
                });
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
                ';
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    /**
     * Render buyers table for AJAX pagination similar to vendors list.
     */
    public function renderBuyersTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $buyersQuery = User::query()
            ->where('role', 'buyer')
            ->with('buyerProfile')
            ->when($request->name, function ($q, $name) {
                $q->where('name', 'like', "{$name}%");
            })
            ->when($request->email, function ($q, $email) {
                $q->where('email', $email);
            })
            ->when($request->phone, function ($q, $phone) {
                $q->where('phone', $phone);
            })
            ->when($request->gst_no, function ($q, $gstNo) {
                $q->whereHas('buyerProfile', function ($q2) use ($gstNo) {
                    $q2->where('gst_no', $gstNo);
                });
            })
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->orderBy('name', 'asc');

        $buyers = $buyersQuery->paginate($perPage, ['*'], 'page', $page);

        return view('admin.buyers._buyers_table', compact('buyers'));
    }

    /**
     * Search buyers for select2 dropdown.
     */
    public function search(Request $request)
    {
        $search = $request->get('q');
        $buyers = User::where('role', 'buyer')
            ->where('name', 'like', '%' . $search . '%')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json($buyers);
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
        $buyer = User::select([
                    'users.*',
                    'buyer_profiles.store_name',
                    'buyer_profiles.email as profile_email',
                    'buyer_profiles.phone as profile_phone',
                    'buyer_profiles.country',
                    'buyer_profiles.state',
                    'buyer_profiles.city',
                    'buyer_profiles.pincode',
                    'buyer_profiles.address',
                    'buyer_profiles.gst_no',
                    'buyer_profiles.gst_doc',
                    'buyer_profiles.store_logo'
                ])
                ->leftJoin('buyer_profiles', 'users.id', '=', 'buyer_profiles.user_id')
                ->where('users.id', $id)
                ->where('users.role', 'buyer')
                ->firstOrFail();

        return view('admin.buyers.edit', compact('buyer'));
    }

    // Update buyer
    public function update(Request $request, $id)
    {
        $buyer = User::with('buyerProfile')
                    ->where('id', $id)
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
            'status' => 'required|in:1,2',
            'store_name' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'state' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'pincode' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'gst_no' => 'nullable|string|max:20',
            'gst_doc' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:1024',
            'store_logo' => 'nullable|file|mimes:jpg,jpeg,png|max:1024',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            $buyer->update($userData);

            $profileData = [
                'store_name' => $request->store_name,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'address' => $request->address,
                'gst_no' => $request->gst_no,
            ];

            if ($request->hasFile('gst_doc')) {
                if ($buyer->buyerProfile && $buyer->buyerProfile->gst_doc) {
                    $oldFilePath = public_path($buyer->buyerProfile->gst_doc);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $gstDocFile = $request->file('gst_doc');
                $gstDocName = uniqid('gst_') . '.' . $gstDocFile->getClientOriginalExtension();
                $gstDocPath = public_path('uploads/buyer/gst_docs');

                if (!file_exists($gstDocPath)) {
                    mkdir($gstDocPath, 0777, true);
                }

                $gstDocFile->move($gstDocPath, $gstDocName);
                $profileData['gst_doc'] = 'uploads/buyer/gst_docs/' . $gstDocName;
            } elseif ($request->has('remove_gst_doc')) {
                if ($buyer->buyerProfile && $buyer->buyerProfile->gst_doc) {
                    $oldFilePath = public_path($buyer->buyerProfile->gst_doc);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $profileData['gst_doc'] = null;
            }

            if ($request->hasFile('store_logo')) {
                if ($buyer->buyerProfile && $buyer->buyerProfile->store_logo) {
                    $oldFilePath = public_path($buyer->buyerProfile->store_logo);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $logoFile = $request->file('store_logo');
                $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = public_path('uploads/buyer/logos');

                if (!file_exists($logoPath)) {
                    mkdir($logoPath, 0777, true);
                }

                $logoFile->move($logoPath, $logoName);
                $profileData['store_logo'] = 'uploads/buyer/logos/' . $logoName;
            } elseif ($request->has('remove_store_logo')) {
                if ($buyer->buyerProfile && $buyer->buyerProfile->store_logo) {
                    $oldFilePath = public_path($buyer->buyerProfile->store_logo);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $profileData['store_logo'] = null;
            }

            if ($buyer->buyerProfile) {
                $buyer->buyerProfile->update($profileData);
            } else {
                $buyer->buyerProfile()->create($profileData);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Buyer updated successfully!',
                    'redirect' => route('admin.buyers.index'),
                ]);
            }

        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to update buyer: ' . $e->getMessage(),
                ], 500);
            }
        }
    }

    // Show buyer details
    public function show($id)
    {
        $buyer = User::select([
                    'users.*',
                    'buyer_profiles.store_name',
                    'buyer_profiles.phone as profile_phone',
                    'buyer_profiles.email as profile_email',
                    'buyer_profiles.country',
                    'buyer_profiles.state',
                    'buyer_profiles.city',
                    'buyer_profiles.pincode',
                    'buyer_profiles.address',
                    'buyer_profiles.gst_no',
                    'buyer_profiles.gst_doc',
                    'buyer_profiles.store_logo'
                ])
                ->leftJoin('buyer_profiles', 'users.id', '=', 'buyer_profiles.user_id')
                ->where('users.id', $id)
                ->where('users.role', 'buyer')
                ->firstOrFail();

        return view('admin.buyers.show', compact('buyer'));
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

    /**
     * Update profile verification status for a buyer.
     */
    public function updateProfileVerification(Request $request)
    {
        try {
            $buyer = User::where('id', $request->id)
                ->where('role', 'buyer')
                ->firstOrFail();

            $buyer->is_profile_verified = $request->is_profile_verified;
            $buyer->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile verification status updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile verification status: ' . $e->getMessage(),
            ], 500);
        }
    }
}