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
            ->with(['children' => function ($q) {
                $q->where('status', 1)->orderBy('name');
            }])
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

    /**
     * Return product and category suggestions for search.
     */
    public function searchSuggestions(Request $request)
    {
        $validated = $request->validate([
            'q' => 'required|string|min:1',
        ]);

        $query = $validated['q'];

        $products = Product::where('status', 'approved')
            ->where('product_name', 'like', "%{$query}%")
            ->select('product_name', 'created_at')
            ->orderBy('product_name')
            ->take(5)
            ->get()
            ->map(function ($item) {
                return [
                    'name' => $item->product_name,
                    'date' => optional($item->created_at)->format('d-m-Y'),
                ];
            });

        $categories = Category::where('status', 1)
            ->where('name', 'like', "%{$query}%")
            ->select('name')
            ->orderBy('name')
            ->take(5)
            ->get();

        return response()->json([
            'products' => $products,
            'categories' => $categories,
        ]);
    }
}
