<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    /**
     * Display product list page.
     */
    public function index()
    {
        return view('buyer.product_list');
    }

    /**
     * Return paginated products in JSON format.
     */
    public function list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'page' => 'sometimes|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $perPage = 24;
        $page = $request->input('page', 1);

        $products = Product::where('status', 'approved')
            ->orderBy('created_at', 'desc')
            ->paginate($perPage, ['*'], 'page', $page);

        $items = $products->getCollection()->map(function ($item) {
            return [
                'id' => $item->id,
                'product_name' => $item->product_name,
                'product_image' => $item->product_image ? asset('storage/' . $item->product_image) : null,
                'date' => optional($item->created_at)->format('d-m-Y'),
            ];
        });

        return response()->json([
            'status' => 1,
            'products' => $items,
            'has_more' => $products->hasMorePages(),
        ]);
    }
}
