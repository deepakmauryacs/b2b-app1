<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('buyer.index');
    }

    /**
     * Return active main categories as JSON.
     */
    public function categories(Request $request)
    {
        $categories = Category::where('status', 1)
            ->where('parent_id', 0)
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($categories);
    }

    /**
     * Return top selling products grouped by name.
     */
    public function topProducts()
    {
        $products = Product::where('status', 'approved')
            ->select('product_name', DB::raw('MAX(product_image) as product_image'))
            ->groupBy('product_name')
            ->orderBy('product_name')
            ->take(8)
            ->get();

        return response()->json($products);
    }
}
