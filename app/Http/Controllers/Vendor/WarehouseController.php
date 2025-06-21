<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Warehouse;

class WarehouseController extends Controller
{
    /**
     * Display the warehouses list page.
     */
    public function index()
    {
        return view('vendor.warehouses.index');
    }

    /**
     * Render paginated warehouses table via AJAX.
     */
    public function renderTable(Request $request)
    {
        $query = Warehouse::withCount('products')->where('vendor_id', Auth::id());

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $warehouses = $query->orderBy('created_at', 'desc')
                           ->paginate($request->input('per_page', 10));

        return view('vendor.warehouses._table', compact('warehouses'));
    }

    /**
     * Store a newly created warehouse via AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'pincode' => 'nullable|regex:/^\d+$/|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        Warehouse::create(array_merge($validator->validated(), [
            'vendor_id' => Auth::id(),
        ]));

        return response()->json([
            'status'  => true,
            'message' => 'Warehouse saved successfully.',
        ]);
    }

    /**
     * Update the specified warehouse via AJAX.
     */
    public function update(Request $request, $id)
    {
        $warehouse = Warehouse::where('id', $id)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name'    => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'city'    => 'nullable|string|max:100',
            'state'   => 'nullable|string|max:100',
            'pincode' => 'nullable|regex:/^\d+$/|max:20',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                'message' => 'Validation failed.',
                'errors'  => $validator->errors(),
            ], 422);
        }

        $warehouse->update($validator->validated());

        return response()->json([
            'status'  => true,
            'message' => 'Warehouse updated successfully.',
        ]);
    }

    /**
     * Remove the specified warehouse via AJAX.
     */
    public function destroy($id)
    {
        $warehouse = Warehouse::where('id', $id)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $warehouse->delete();

        return response()->json([
            'status'  => true,
            'message' => 'Warehouse deleted successfully.',
        ]);
    }
}

