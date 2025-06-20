<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    public function index()
    {
        return view('admin.products.all');
    }

    public function renderProductsTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page = $request->input('page', 1);

        $productsQuery = Product::with(['vendor', 'category']);

        if ($request->product_name) {
            $productsQuery->whereRaw('MATCH(product_name) AGAINST(? IN BOOLEAN MODE)', [$request->product_name]);
        }

        $productsQuery->when($request->vendor_id, function ($query, $vendorId) {
            $query->where('vendor_id', $vendorId);
        });

        $productsQuery->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
            $query->where('status', $request->status);
        });

        $productsQuery->orderBy('created_at', 'desc');

        $products = $productsQuery->paginate($perPage, ['*'], 'page', $page);

        return view('admin.products._all_products_table', compact('products'));
    }
}
