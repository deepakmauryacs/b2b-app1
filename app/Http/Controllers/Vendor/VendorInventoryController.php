<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockLog;
use App\Models\Product;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class VendorInventoryController extends Controller
{
    /**
     * Display inventory list page
     */
    public function index()
    {
        $warehouses = Auth::user()->warehouses()->orderBy('name')->get();
        return view('vendor.inventory.index', compact('warehouses'));
    }

    /**
     * Render paginated inventory table via AJAX
     */
    public function renderInventoryTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $warehouseId = $request->input('warehouse_id');

        $warehouses = Auth::user()->warehouses()->orderBy('name')->get();

        $productsQuery = Product::with(['latestStockLog', 'warehouseStocks' => function($q) use ($warehouseId) {
                if ($warehouseId) {
                    $q->where('warehouse_id', $warehouseId);
                }
            }])
            ->where('vendor_id', Auth::id());

        if ($request->filled('product_name')) {
            $productsQuery->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($warehouseId) {
            $productsQuery->whereHas('warehouses', function($q) use ($warehouseId) {
                $q->where('warehouses.id', $warehouseId);
            });
        }

        $products = $productsQuery->orderBy('created_at', 'desc')->paginate($perPage);

        return view('vendor.inventory._inventory_table', compact('products', 'warehouseId', 'warehouses'));
    }

    /**
     * Get product quantity for specific warehouse via AJAX
     */
    public function getWarehouseStock(Request $request, $id)
    {
        $warehouseId = $request->input('warehouse_id');

        if(!$warehouseId){
            return response()->json([
                'status' => 0,
                'message' => 'Warehouse is required.'
            ], 422);
        }

        $product = Product::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $warehouse = Warehouse::where('id', $warehouseId)->where('vendor_id', Auth::id())->firstOrFail();

        $qty = $product->stockInWarehouse($warehouse->id);

        return response()->json([
            'status' => 1,
            'quantity' => $qty,
        ]);
    }

    /**
     * Update stock quantity for product
     */
    public function updateStock(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();

        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|exists:warehouses,id',
            'in_stock'  => 'nullable|integer|min:0',
            'out_stock' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $warehouse = Warehouse::where('id', $request->warehouse_id)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $in  = (int) $request->input('in_stock', 0);
        $out = (int) $request->input('out_stock', 0);

        if($in === 0 && $out === 0){
            return response()->json([
                'status' => 0,
                'message' => 'Please provide quantity to add or remove.'
            ], 422);
        }

        $oldQty = $product->stock_quantity;
        $newQty = $oldQty + $in - $out;

        $wp = WarehouseProduct::firstOrNew([
            'warehouse_id' => $warehouse->id,
            'product_id'   => $product->id,
        ]);
        $whOldQty = $wp->exists ? $wp->quantity : 0;
        $whNewQty = $whOldQty + $in - $out;
        if($whNewQty < 0){
            return response()->json([
                'status' => 0,
                'message' => 'Resulting stock cannot be negative.'
            ], 422);
        }

        if($newQty < 0){
            return response()->json([
                'status' => 0,
                'message' => 'Resulting stock cannot be negative.'
            ], 422);
        }

        $product->stock_quantity = $newQty;
        $product->save();
        $wp->quantity = $whNewQty;
        $wp->save();

        StockLog::create([
            'product_id'   => $product->id,
            'user_id'      => Auth::id(),
            'old_quantity' => $oldQty,
            'new_quantity' => $product->stock_quantity,
        ]);

        return response()->json([
            'status' => 1,
            'message' => 'Stock updated successfully.'
        ]);
    }

    /**
     * Display stock logs for a product
     */
    public function stockLogs(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'per_page' => 'sometimes|integer|in:10,25,50,100',
            'page'     => 'sometimes|integer|min:1'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 0,
                    'message' => $validator->errors()->first()
                ], 422);
            }
            return back()->withErrors($validator);
        }

        $product = Product::where('id', $id)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        $logs = StockLog::with(['user', 'product'])
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate($request->input('per_page', 10));

        if ($request->ajax()) {
            return view('vendor.inventory._stock_logs', compact('logs'));
        }

        return view('vendor.inventory.logs', compact('logs', 'product'));
    }

    /**
     * Initialize export by returning total records and chunk size
     */
    public function exportInit()
    {
        $total = Product::where('vendor_id', Auth::id())->count();
        return response()->json([
            'total' => $total,
            'chunk_size' => 500,
        ]);
    }

    /**
     * Provide a chunk of inventory data for export via AJAX
     */
    public function exportChunk(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'offset' => 'required|integer|min:0',
            'limit'  => 'required|integer|min:1|max:1000',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => 0,
                'message' => $validator->errors()->first(),
            ], 422);
        }

        $offset = (int) $request->input('offset');
        $limit  = (int) $request->input('limit');

        $products = Product::where('vendor_id', Auth::id())
            ->with('warehouses')
            ->orderBy('id')
            ->skip($offset)
            ->take($limit)
            ->get();

        $rows = [];
        foreach ($products as $product) {
            $totalQty = $product->stock_quantity;
            if ($product->warehouses->count()) {
                foreach ($product->warehouses as $wh) {
                    $rows[] = [
                        'product_name'   => $product->product_name,
                        'warehouse_name' => $wh->name,
                        'quantity'       => $wh->pivot->quantity,
                        'total_quantity' => $totalQty,
                        'updated_at'     => $product->updated_at->format('d-m-Y'),
                    ];
                }
            } else {
                $rows[] = [
                    'product_name'   => $product->product_name,
                    'warehouse_name' => '',
                    'quantity'       => $product->stock_quantity,
                    'total_quantity' => $totalQty,
                    'updated_at'     => $product->updated_at->format('d-m-Y'),
                ];
            }
        }

        return response()->json(['rows' => $rows]);
    }
}
