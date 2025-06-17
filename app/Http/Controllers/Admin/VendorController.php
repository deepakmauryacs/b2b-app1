<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Cache;
use Log; // Make sure to include Log for error logging

class VendorController extends Controller
{

    public function index()
    {
        return view('admin.vendors.index');
    }


    public function renderVendorsTable(Request $request)
    {
        // Define items per page, default to 10 if not provided
        $perPage = $request->input('per_page', 10);
        // Define current page, default to 1 if not provided
        $page = $request->input('page', 1);

        // Build the base query for vendors
        $vendorsQuery = User::query()
            ->where('role', 'vendor') // Efficient with index on `users.role`
            ->with('vendorProfile') // Eager load vendorProfile. Efficient with index on `vendor_profiles.user_id`
            // --- CORRECTED: Restored arguments for withCount() ---
            ->withCount([ // Count approved and pending products
                'products as approved_products_count' => function ($q) {
                    $q->where('status', 'approved');
                },
                'products as pending_products_count' => function ($q) {
                    $q->where('status', 'pending');
                }
            ]);
            // --- END CORRECTED ---

        // Apply filters based on request parameters
        // --- IMPORTANT OPTIMIZATIONS FOR LARGE TABLES ---
        // For columns frequently used in WHERE clauses, especially with large datasets,
        // standard B-tree indexes are highly effective for exact matches or leading-prefix searches ('value%').
        // 'LIKE %value%' (leading wildcard) is very inefficient as it cannot use a standard index.
        // If true 'anywhere in string' search is required, consider full-text indexing or external search solutions (e.g., Elasticsearch).

        $vendorsQuery->when($request->name, function($q, $name) {
            // Changed from LIKE "%{$name}%" to LIKE "{$name}%" to utilize index on 'name' column.
            // This allows the `idx_users_name` index to be used for filtering.
            $q->where('name', 'like', "{$name}%");
        })
        ->when($request->phone, function($q, $phone) {
            // Changed from LIKE "%{$phone}%" to exact match.
            // Phone numbers are typically searched for exact matches.
            // This will efficiently use an index on `phone` (e.g., `idx_users_phone`).
            $q->where('phone', $phone);
        })
        ->when($request->email, function($q, $email) {
            // Changed from LIKE "%{$email}%" to exact match.
            // The `email` column already has a `UNIQUE KEY` index, which is highly efficient for exact matches.
            $q->where('email', $email);
        })
        ->when($request->status!== null && $request->status!== '', function($q) use ($request) {
            // This filter is efficient with an index on `users.status` (`idx_users_status`).
            $q->where('status', (int) $request->status);
        })
        ->when($request->gst_no, function($q, $gstNo) {
            // This already uses an exact match and benefits from the `UNIQUE` index on `vendor_profiles.gst_no`.
            $q->whereHas('vendorProfile', function($q2) use ($gstNo) {
                $q2->where('gst_no', $gstNo);
            });
        })
        ->orderBy('name', 'asc'); // Ensures consistent order for pagination. Benefits from index on `name`.

        // Apply Laravel's pagination to the query builder result
        // Laravel's paginate handles LIMIT and OFFSET, which are generally efficient.
        // The performance of the underlying COUNT(*) query for pagination will depend on the efficiency of the applied filters.
        $vendors = $vendorsQuery->paginate($perPage, ['*'], 'page', $page);

        // Return the partial Blade view with the paginated data
        return view('admin.vendors._vendors_table', compact('vendors'));
    }

    public function search(Request $request)
    {
        $search = $request->get('q');
        $vendors = \App\Models\User::where('role', 'vendor')
            ->where('name', 'like', '%' . $search . '%')
            ->limit(20)
            ->get(['id', 'name']);

        return response()->json($vendors);
    }



    public function edit($id)
    {
        $vendor = User::select([
                    'users.*',
                    'vendor_profiles.store_name',
                    'vendor_profiles.email as profile_email',
                    'vendor_profiles.phone as profile_phone',
                    'vendor_profiles.country',
                    'vendor_profiles.state',
                    'vendor_profiles.city',
                    'vendor_profiles.pincode',
                    'vendor_profiles.address',
                    'vendor_profiles.gst_no',
                    'vendor_profiles.gst_doc',
                    'vendor_profiles.store_logo',
                    'vendor_profiles.accept_terms'
                ])
                ->leftJoin('vendor_profiles', 'users.id', '=', 'vendor_profiles.user_id')
                ->where('users.id', $id)
                ->where('users.role', 'vendor')
                ->firstOrFail();

        return view('admin.vendors.edit', compact('vendor'));
    }

    // Update vendor
    public function update(Request $request, $id)
    {
        $vendor = User::with('vendorProfile')
                    ->where('id', $id)
                    ->where('role', 'vendor')
                    ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($vendor->id)
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
                    'errors' => $validator->errors()
                ], 422);
            }
        }

        try {
            DB::beginTransaction();

            // Update user data
            $userData = [
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status,
            ];

            if ($request->filled('password')) {
                $userData['password'] = bcrypt($request->password);
            }

            $vendor->update($userData);

            // Prepare vendor profile data
            $profileData = [
                'store_name' => $request->store_name,
                'country' => $request->country,
                'state' => $request->state,
                'city' => $request->city,
                'pincode' => $request->pincode,
                'address' => $request->address,
                'gst_no' => $request->gst_no,
            ];

            // Handle GST document upload
            if ($request->hasFile('gst_doc')) {
                // Delete old file if exists
                if ($vendor->vendorProfile && $vendor->vendorProfile->gst_doc) {
                    $oldFilePath = public_path($vendor->vendorProfile->gst_doc);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $gstDocFile = $request->file('gst_doc');
                $gstDocName = uniqid('gst_') . '.' . $gstDocFile->getClientOriginalExtension();
                $gstDocPath = public_path('uploads/vendor/gst_docs');

                if (!file_exists($gstDocPath)) {
                    mkdir($gstDocPath, 0777, true);
                }

                $gstDocFile->move($gstDocPath, $gstDocName);
                $profileData['gst_doc'] = 'uploads/vendor/gst_docs/' . $gstDocName;
            } elseif ($request->has('remove_gst_doc')) {
                if ($vendor->vendorProfile && $vendor->vendorProfile->gst_doc) {
                    $oldFilePath = public_path($vendor->vendorProfile->gst_doc);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $profileData['gst_doc'] = null;
            }

            // Handle store logo upload
            if ($request->hasFile('store_logo')) {
                // Delete old file if exists
                if ($vendor->vendorProfile && $vendor->vendorProfile->store_logo) {
                    $oldFilePath = public_path($vendor->vendorProfile->store_logo);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }

                $logoFile = $request->file('store_logo');
                $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
                $logoPath = public_path('uploads/vendor/logos');

                if (!file_exists($logoPath)) {
                    mkdir($logoPath, 0777, true);
                }

                $logoFile->move($logoPath, $logoName);
                $profileData['store_logo'] = 'uploads/vendor/logos/' . $logoName;
            } elseif ($request->has('remove_store_logo')) {
                if ($vendor->vendorProfile && $vendor->vendorProfile->store_logo) {
                    $oldFilePath = public_path($vendor->vendorProfile->store_logo);
                    if (file_exists($oldFilePath)) {
                        unlink($oldFilePath);
                    }
                }
                $profileData['store_logo'] = null;
            }

            // Update or create vendor profile
            if ($vendor->vendorProfile) {
                $vendor->vendorProfile->update($profileData);
            } else {
                $vendor->vendorProfile()->create($profileData);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Vendor updated successfully!',
                    'redirect' => route('admin.vendors.index')
                ]);
            }


        } catch (\Exception $e) {
            DB::rollBack();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to update vendor: ' . $e->getMessage()
                ], 500);
            }


        }
    }

    public function show($id)
    {
        $vendor = User::select([
                    'users.*',
                    'vendor_profiles.store_name',
                    'vendor_profiles.email as profile_email',
                    'vendor_profiles.phone as profile_phone',
                    'vendor_profiles.country',
                    'vendor_profiles.state',
                    'vendor_profiles.city',
                    'vendor_profiles.pincode',
                    'vendor_profiles.address',
                    'vendor_profiles.gst_no',
                    'vendor_profiles.gst_doc',
                    'vendor_profiles.store_logo',
                    'vendor_profiles.accept_terms'
                ])
                ->leftJoin('vendor_profiles', 'users.id', '=', 'vendor_profiles.user_id')
                ->where('users.id', $id)
                ->where('users.role', 'vendor')
                ->firstOrFail();

        return view('admin.vendors.show', compact('vendor'));
    }


    public function updateProfileVerification(Request $request)
    {
        try {
            $vendor = User::findOrFail($request->id);
            $vendor->is_profile_verified = $request->is_profile_verified;
            $vendor->save();

            return response()->json([
                'success' => true,
                'message' => 'Profile verification status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating profile verification status: ' . $e->getMessage()
            ], 500);
        }
    }


}
