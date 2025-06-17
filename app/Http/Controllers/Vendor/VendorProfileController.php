<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class VendorProfileController extends Controller
{

    public function profile()
    {
        $vendor = auth()->user()->vendorProfile; // Get the vendor profile for the logged-in user
        return view('vendor.vendor-profile', compact('vendor'));
    }


    public function update1(Request $request)
    {
        $user = Auth::user();
        $vendorProfile = $user->vendorProfile;

        $validator = Validator::make($request->all(), [
            'store_name'    => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:15',
            'country'       => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'city'          => 'required|string|max:100',
            'pincode'       => 'required|string|max:20',
            'address'       => 'required|string|max:255',
            'gst_doc'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'store_logo'    => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'accept_terms'  => 'accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle gst_doc upload
        if ($request->hasFile('gst_doc')) {
            $gstDocFile = $request->file('gst_doc');
            $gstDocName = uniqid('gst_') . '.' . $gstDocFile->getClientOriginalExtension();
            $gstDocPath = public_path('uploads/vendor/gst_docs');
            if (!file_exists($gstDocPath)) {
                mkdir($gstDocPath, 0777, true);
            }
            $gstDocFile->move($gstDocPath, $gstDocName);
            $validated['gst_doc'] = 'uploads/vendor/gst_docs/' . $gstDocName;
        }

        // Handle store_logo upload
        if ($request->hasFile('store_logo')) {
            $logoFile = $request->file('store_logo');
            $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
            $logoPath = public_path('uploads/vendor/logos');
            if (!file_exists($logoPath)) {
                mkdir($logoPath, 0777, true);
            }
            $logoFile->move($logoPath, $logoName);
            $validated['store_logo'] = 'uploads/vendor/logos/' . $logoName;
        }

        // Save or update profile
        if ($vendorProfile) {
            $vendorProfile->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            \App\Models\VendorProfile::create($validated);
        }

        return response()->json([
            'status' => true,
            'reload' => true,
            'message' => 'Profile updated successfully!',
        ]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();
        $vendorProfile = $user->vendorProfile;

        $validator = Validator::make($request->all(), [
            'store_name'    => 'required|string|max:255',
            'email'         => 'required|email|max:255',
            'phone'         => 'required|string|max:15',
            'country'       => 'required|string|max:100',
            'state'         => 'required|string|max:100',
            'city'          => 'required|string|max:100',
            'pincode'       => 'required|string|max:20',
            'address'       => 'required|string|max:255',
            'gst_doc'       => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'store_logo'    => 'nullable|file|mimes:jpg,jpeg,png|max:2048',
            'accept_terms'  => 'accepted',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $validated = $validator->validated();

        // Handle gst_doc upload
        if ($request->hasFile('gst_doc')) {
            // Delete old gst_doc if exists
            if ($vendorProfile && $vendorProfile->gst_doc) {
                $oldGstDocPath = public_path($vendorProfile->gst_doc);
                if (file_exists($oldGstDocPath)) {
                    unlink($oldGstDocPath);
                }
            }

            $gstDocFile = $request->file('gst_doc');
            $gstDocName = uniqid('gst_') . '.' . $gstDocFile->getClientOriginalExtension();
            $gstDocPath = public_path('uploads/vendor/gst_docs');
            
            if (!file_exists($gstDocPath)) {
                mkdir($gstDocPath, 0777, true);
            }
            
            $gstDocFile->move($gstDocPath, $gstDocName);
            $validated['gst_doc'] = 'uploads/vendor/gst_docs/' . $gstDocName;
        } else {
            // Keep the existing gst_doc if no new file was uploaded
            unset($validated['gst_doc']);
        }

        // Handle store_logo upload
        if ($request->hasFile('store_logo')) {
            // Delete old store_logo if exists
            if ($vendorProfile && $vendorProfile->store_logo) {
                $oldLogoPath = public_path($vendorProfile->store_logo);
                if (file_exists($oldLogoPath)) {
                    unlink($oldLogoPath);
                }
            }

            $logoFile = $request->file('store_logo');
            $logoName = uniqid('logo_') . '.' . $logoFile->getClientOriginalExtension();
            $logoPath = public_path('uploads/vendor/logos');
            
            if (!file_exists($logoPath)) {
                mkdir($logoPath, 0777, true);
            }
            
            $logoFile->move($logoPath, $logoName);
            $validated['store_logo'] = 'uploads/vendor/logos/' . $logoName;
        } else {
            // Keep the existing store_logo if no new file was uploaded
            unset($validated['store_logo']);
        }

        // Save or update profile
        if ($vendorProfile) {
            $vendorProfile->update($validated);
        } else {
            $validated['user_id'] = $user->id;
            \App\Models\VendorProfile::create($validated);
        }

        return response()->json([
            'status' => true,
            'reload' => true,
            'message' => 'Profile updated successfully!',
        ]);
    }


}
