<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockLog;
use App\Models\Product;
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
        return view('vendor.inventory.index');
    }

    /**
     * Render paginated inventory table via AJAX
     */
    public function renderInventoryTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $productsQuery = Product::where('vendor_id', Auth::id());

        if ($request->filled('product_name')) {
            $productsQuery->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        $products = $productsQuery->orderBy('created_at', 'desc')->paginate($perPage);

        return view('vendor.inventory._inventory_table', compact('products'));
    }

    /**
     * Update stock quantity for product
     */
    public function updateStock(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();

        $validator = Validator::make($request->all(), [
            'stock_quantity' => 'required|integer|min:0'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first()
            ], 422);
        }

        $oldQty = $product->stock_quantity;
        $product->stock_quantity = $request->stock_quantity;
        $product->save();

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
     * Fetch stock logs for a product via AJAX
     */
    public function stockLogs(Request $request, $id)
    {
        $logs = StockLog::with('user')
            ->where('product_id', $id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('vendor.inventory._stock_logs', compact('logs'));
    }
}
