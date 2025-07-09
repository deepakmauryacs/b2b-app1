<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use App\Models\Category;
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
}
