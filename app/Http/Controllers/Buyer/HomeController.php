<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class HomeController extends Controller
{
    public function index()
    {
        return view('buyer.index');
    }

    /**
     * Display sub category page for selected category.
     */
    public function subCategoryPage($slug)
    {
        $data = Validator::make(['slug' => $slug], [
            'slug' => 'required|string|exists:categories,slug',
        ])->validate();

        $category = Category::where('slug', $data['slug'])
            ->where('parent_id', 0)
            ->where('status', 1)
            ->firstOrFail();

        $subcategories = Category::where('status', 1)
            ->where('parent_id', $category->id)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('buyer.subcategories', [
            'category' => $category,
            'subcategories' => $subcategories,
        ]);
    }

    /**
     * Display product page for selected sub category.
     */
    public function productPage($subCategoryId)
    {
        $data = Validator::make(['id' => $subCategoryId], [
            'id' => 'required|integer|exists:categories,id',
        ])->validate();

        return view('buyer.products', ['subCategoryId' => $data['id']]);
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
            ->get(['id', 'name', 'slug']);

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
            'q' => ['required', 'string', 'min:1', 'regex:/^[\pL\pN\s]+$/u'],
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

    /**
     * Return sub categories for given category.
     */
    public function subCategories($categoryId)
    {
        $data = Validator::make(['id' => $categoryId], [
            'id' => 'required|integer|exists:categories,id',
        ])->validate();

        $sub = Category::where('status', 1)
            ->where('parent_id', $data['id'])
            ->orderBy('name')
            ->get(['id', 'name']);

        return response()->json($sub);
    }

    /**
     * Return products list for selected sub category.
     */
    public function productsBySubCategory($subCategoryId)
    {
        $data = Validator::make(['id' => $subCategoryId], [
            'id' => 'required|integer|exists:categories,id',
        ])->validate();

        $products = Product::where('status', 'approved')
            ->where('sub_category_id', $data['id'])
            ->orderBy('product_name')
            ->get(['product_name', 'product_image']);

        return response()->json($products);
    }
}
