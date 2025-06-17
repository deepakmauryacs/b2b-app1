<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class PendingProductController extends Controller
{
    public function index()
    {    
        
        return view('admin.products.pending');
    }

    public function getPendingProducts(Request $request)
    {
        $products = Product::with(['vendor', 'category'])
            ->where('status', 'pending')
            ->when($request->product_name, function($query, $name) {
                $query->where('product_name', 'like', "%{$name}%");
            })
            ->when($request->vendor_id, function($query, $vendorId) {
                $query->where('vendor_id', $vendorId);
            })
            ->orderBy('created_at', 'desc');

        return DataTables::of($products)
            ->addIndexColumn()
            ->addColumn('vendor', function($product) {
                return $product->vendor->name ?? 'N/A';
            })
            ->addColumn('category', function($product) {
                return $product->category->name ?? 'N/A';
            })
            ->editColumn('price', function($product) {
                return '₹' . number_format($product->price, 2);
            })
            ->editColumn('created_at', function($product) {
                return $product->created_at->format('d M Y');
            })
            ->addColumn('action', function($product) {
                    return '
                        <div class="d-flex gap-2">
                            <a href="'.route('admin.products.pending.show', $product->id).'" class="btn btn-sm btn-soft-info">
                                <i class="bi bi-eye"></i> 
                            </a>
                            <button class="btn btn-sm btn-soft-success approve-product" data-id="'.$product->id.'">
                                <i class="bi bi-check-circle"></i> 
                            </button>
                            <button class="btn btn-sm btn-soft-danger reject-product" data-id="'.$product->id.'">
                                <i class="bi bi-x-circle"></i> 
                            </button>
                        </div>
                    ';
                })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function show($id)
    {
        $product = Product::with(['vendor', 'category'])->findOrFail($id);
        return view('admin.products.show', compact('product'));
    }

    public function approve($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'approved']);

        return response()->json([
            'success' => true,
            'message' => 'Product approved successfully!'
        ]);
    }

    public function reject($id)
    {
        $product = Product::findOrFail($id);
        $product->update(['status' => 'rejected']);

        return response()->json([
            'success' => true,
            'message' => 'Product rejected successfully!'
        ]);
    }
}