<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Exports\VendorsExport;
use Maatwebsite\Excel\Facades\Excel;
use Maatwebsite\Excel\Excel as ExcelFormat;
use Illuminate\Support\Facades\Storage;

class VendorController extends Controller
{
    // Show all vendors
    public function index()
    {
        return view('admin.vendors.index');
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


    // Add this method to your VendorController
    // public function export()
    // {
    //     return Excel::download(new VendorsExport, 'vendors.xlsx', ExcelFormat::XLSX);
    // }

    public function startExport(Request $request)
    {
        try {
            // Generate a unique export ID
            $exportId = Str::uuid()->toString();
            $filePath = 'exports/vendors_' . $exportId . '.xlsx';

            // Store initial progress
            cache()->put('export_progress_' . $exportId, [
                'progress' => 0,
                'message' => 'Preparing export...',
                'status' => 'started',
                'file_path' => $filePath
            ], now()->addMinutes(30));

            // Start export in background (using a queue for large datasets)
            dispatch(function () use ($exportId, $filePath) {
                // Simulate progress updates (replace with actual logic if needed)
                for ($i = 10; $i <= 90; $i += 10) {
                    cache()->put('export_progress_' . $exportId, [
                        'progress' => $i,
                        'message' => 'Exporting data... (' . $i . '%)',
                        'status' => 'processing',
                        'file_path' => $filePath
                    ], now()->addMinutes(30));
                    sleep(1); // Simulate processing time
                }

                // Generate and store the Excel file
                Excel::store(new VendorsExport, $filePath, 'public');

                // Mark as completed
                cache()->put('export_progress_' . $exportId, [
                    'progress' => 100,
                    'message' => 'Export completed!',
                    'status' => 'completed',
                    'file_path' => $filePath
                ], now()->addMinutes(30));
            })->onQueue('exports');

            return response()->json(['status' => 'started', 'export_id' => $exportId]);
        } catch (\Exception $e) {
            \Log::error('Export start failed: ' . $e->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Failed to start export'], 500);
        }
    }

    public function getProgress(Request $request)
    {
        $exportId = $request->query('export_id');
        $progress = cache()->get('export_progress_' . $exportId, [
            'progress' => 0,
            'message' => 'Export not found',
            'status' => 'failed'
        ]);

        return response()->json($progress);
    }

    public function download(Request $request)
    {
        $exportId = $request->query('export_id');
        $progress = cache()->get('export_progress_' . $exportId);

        if (!$progress || $progress['status'] !== 'completed') {
            return redirect()->back()->with('error', 'Export not ready or invalid.');
        }

        try {
            $filePath = $progress['file_path'];
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->download($filePath, 'vendors.xlsx');
            }

            return redirect()->back()->with('error', 'File not found.');
        } catch (\Exception $e) {
            \Log::error('Export download failed: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download export.');
        }
    }

    // Fetch vendors for DataTable
    public function getVendors(Request $request)
    {
        $vendors = User::query()
            ->where('role', 'vendor')
            ->with('vendorProfile') // eager load instead of join
            ->withCount([
                'products as approved_products_count' => function ($q) {
                    $q->where('status', 'approved');
                },
                'products as pending_products_count' => function ($q) {
                    $q->where('status', 'pending');
                }
            ])
            ->when($request->name, fn($q, $name) =>
                $q->where('name', 'like', "%{$name}%")
            )
            ->when($request->email, fn($q, $email) =>
                $q->where('email', 'like', "%{$email}%")
            )
            ->when($request->status !== null, fn($q) =>
                $q->where('status', $request->status)
            )
            ->when($request->store_name, fn($q, $storeName) =>
                $q->whereHas('vendorProfile', fn($q2) =>
                    $q2->where('store_name', 'like', "%{$storeName}%")
                )
            )
            ->when($request->gst_no, fn($q, $gstNo) =>
                $q->whereHas('vendorProfile', fn($q2) =>
                    $q2->where('gst_no', 'like', "%{$gstNo}%")
                )
            )
            ->orderBy('name');

        return DataTables::of($vendors)
            ->addIndexColumn()
            ->editColumn('status', function ($vendor) {
                $status = $vendor->status == 1 ? 'active' : 'inactive';
                $class = $vendor->status == 1
                    ? 'badge border border-success text-success px-2 py-1 fs-13'
                    : 'badge border border-danger text-danger px-2 py-1 fs-13';
                return '<span class="' . $class . '">' . ucfirst($status) . '</span>';
            })
            ->editColumn('is_profile_verified', function ($vendor) {
                $checked = $vendor->is_profile_verified == 1 ? 'checked' : '';
                return '
                    <div class="form-check form-switch">
                        <input type="checkbox" class="form-check-input profile-verified-toggle"
                            data-id="' . $vendor->id . '" ' . $checked . '>
                    </div>
                ';
            })
            ->addColumn('store_info', function ($vendor) {
                $profile = $vendor->vendorProfile;
                $info = [];
                if ($profile?->gst_no) {
                    $info[] = '<b>GST:</b> ' . $profile->gst_no;
                }
                if ($profile?->pincode) {
                    $info[] = '<b>Pincode:</b> ' . $profile->pincode;
                }
                if ($profile?->address) {
                    $info[] = '<b>Address:</b> ' . Str::limit($profile->address, 150);
                }
                return implode('<br>', $info);
            })
            ->addColumn('products_info', function ($vendor) {
                return '
                    <div class="gap-2">
                        <span class="badge bg-success">Approved: ' . $vendor->approved_products_count . '</span>
                        <span class="badge bg-warning">Pending: ' . $vendor->pending_products_count . '</span>
                    </div>
                ';
            })
            ->editColumn('created_at', function ($vendor) {
                return \Carbon\Carbon::parse($vendor->created_at)->format('d M Y');
            })
            ->addColumn('action', function ($vendor) {
                return '
                    <div class="d-flex gap-2">
                        <a href="' . route('admin.vendors.show', $vendor->id) . '" class="btn btn-sm btn-soft-info" title="View">
                            <iconify-icon icon="solar:eye-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                        <a href="' . route('admin.vendors.edit', $vendor->id) . '" class="btn btn-sm btn-soft-primary" title="Edit">
                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                    </div>
                ';
            })
            ->rawColumns(['status', 'is_profile_verified', 'store_info', 'products_info', 'action'])
            ->make(true);
    }



    // Show create vendor form
    public function create()
    {
        return view('admin.vendors.create');
    }

    // Store new vendor
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
            $vendor = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'role' => 'vendor',
                'password' => bcrypt($request->password),
                'status' => $request->status
            ]);

            return redirect()
                ->route('admin.vendors.index')
                ->with('success', 'Vendor created successfully!');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to create vendor: ' . $e->getMessage())
                ->withInput();
        }
    }

    // Show edit vendor form
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