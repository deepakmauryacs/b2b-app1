<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class ApprovedProductController extends Controller
{
    public function index()
    {
        return view('admin.products.approved');
    }

    public function renderApprovedProductsTable(Request $request)
    {
        // Define items per page, default to 10
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        // Build query
        $productsQuery = Product::with(['vendor', 'category'])
            ->where('status', 'approved');

        // --- IMPORTANT CHANGE FOR FULLTEXT SEARCH ---
        if ($request->product_name) {
            // Use whereRaw for MATCH AGAINST syntax
            // The `*` is a placeholder for `product_name` in the prepared statement
            // The `?` is a placeholder for the search term
            // `IN BOOLEAN MODE` allows for more flexible searching (e.g., +word -another)
            $productsQuery->whereRaw('MATCH(product_name) AGAINST(? IN BOOLEAN MODE)', [$request->product_name]);
        }
        // --- END IMPORTANT CHANGE ---

        $productsQuery->when($request->vendor_id, function($query, $vendorId) {
            $query->where('vendor_id', $vendorId);
        })
        ->orderBy('created_at', 'desc');

        // Apply pagination
        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        // Return Blade view (create this partial)
        return view('admin.products._approved_products_table', compact('products'));
    }


    public function show($id)
    {
        $product = Product::with(['vendor', 'category'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function revokeApproval($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'pending']);

        return response()->json([
            'success' => true,
            'message' => 'Product approval revoked successfully!'
        ]);
    }
}