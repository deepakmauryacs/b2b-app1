<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VendorProductController extends Controller
{

    // Show vendor's all products
    public function index()
    {
        return view('vendor.products.index', [
            'pageTitle' => 'All Product List'
        ]);
    }

    // Show approved products list
    public function approved()
    {
        return view('vendor.products.index', [
            'statusDefault' => 'approved',
            'pageTitle' => 'Approved Products'
        ]);
    }

    // Show pending products list
    public function pending()
    {
        return view('vendor.products.index', [
            'statusDefault' => 'pending',
            'pageTitle' => 'Pending Products'
        ]);
    }

    // Show rejected products list
    public function rejected()
    {
        return view('vendor.products.index', [
            'statusDefault' => 'rejected',
            'pageTitle' => 'Rejected Products'
        ]);
    }

    // Fetch products for DataTable
    public function getProducts(Request $request)
    {
        $query = Product::where('vendor_id', Auth::id());

        if ($request->filled('product_name')) {
            $query->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $products = $query->latest();

        return DataTables::of($products)
            ->addIndexColumn()
            ->editColumn('product_image', function ($product) {
                return $product->product_image
                    ? '<img src="' . asset('storage/' . $product->product_image) . '" width="50">'
                    : 'N/A';
            })
           ->editColumn('status', function ($product) {
                // Determine the badge classes based on status
                if ($product->status === 'approved') {
                    $class = 'badge border border-success text-success px-2 py-1 fs-13';
                } elseif ($product->status === 'pending') {
                    $class = 'badge border border-warning text-warning px-2 py-1 fs-13';
                } else { // 'rejected' or any other status
                    $class = 'badge border border-danger text-danger px-2 py-1 fs-13';
                }

                // Return the span with the appropriate classes
                return '<span class="'. $class .'">'. ucfirst($product->status) .'</span>';
            })
            ->editColumn('created_at', function ($product) {
                return Carbon::parse($product->created_at)->format('d-m-Y');
            })

            ->addColumn('action', function ($product) {
                return '
                    <a href="' . route('vendor.products.show', $product->id) . '" class="btn btn-sm btn-soft-info me-1 view-product">
                        <i class="bi bi-eye"></i>
                    </a>
                    <a href="' . route('vendor.products.edit', $product->id) . '" class="btn btn-sm btn-soft-warning me-1">
                        <i class="bi bi-pencil"></i>
                    </a>
                    <button class="btn btn-sm btn-soft-danger delete-product" data-id="' . $product->id . '">
                        <i class="bi bi-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['product_image', 'status', 'action'])
            ->make(true);
    }

    // Render paginated products table for AJAX requests
    public function renderProductsTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);

        $productsQuery = Product::where('vendor_id', Auth::id());

        if ($request->filled('product_name')) {
            $productsQuery->where('product_name', 'like', '%' . $request->product_name . '%');
        }

        if ($request->filled('status')) {
            $productsQuery->where('status', $request->status);
        }

        $products = $productsQuery->orderBy('created_at', 'desc')->paginate($perPage);

        return view('vendor.products._products_table', compact('products'));
    }

    // Show create product form
    public function create()
    {
        // Fetch active categories
        $categories = DB::table('categories')
            ->where('status', 1)->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();
        return view('vendor.products.create', compact('categories'));
    }

    public function getSubcategories($parentId)
    {
        $subcategories = DB::table('categories')
                            ->where('parent_id', $parentId)
                            ->where('status', 1)
                            ->orderBy('name', 'ASC')
                            ->get();

        return response()->json($subcategories);
    }


    // Save product
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'product_name' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'slug' => 'nullable|string|unique:products,slug',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'sub_category_id' => 'nullable|exists:categories,id',
            'description' => 'nullable|string',
            'unit' => 'nullable|string|max:50',
            'min_order_qty' => 'nullable|integer|min:1',
            'stock_quantity' => 'nullable|integer|min:0',
            'hsn_code' => 'nullable|string|max:50',
            'gst_rate' => 'nullable|numeric|min:0|max:100',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $product = new Product();

            // Sanitize input data
            $product_name = strip_tags(trim($request->product_name));
            $slug = $request->slug ? strip_tags(trim($request->slug)) : Str::slug($product_name);
            $description = $request->description ? strip_tags(trim($request->description)) : null;
            $unit = $request->unit ? strip_tags(trim($request->unit)) : null;
            $hsn_code = $request->hsn_code ? strip_tags(trim($request->hsn_code)) : null;

            // Set product properties
            $product->vendor_id = Auth::id();
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->product_name = $product_name;
            $product->slug = $slug;
            $product->description = $description;
            $product->price = $request->price;
            $product->unit = $unit;
            $product->min_order_qty = $request->min_order_qty ?? 1;
            $product->stock_quantity = $request->stock_quantity ?? 0;
            $product->hsn_code = $hsn_code;
            $product->gst_rate = $request->gst_rate;
            $product->status = 'pending';

            // Handle image upload
            if ($request->hasFile('product_image')) {
                $product->product_image = $request->file('product_image')->store('uploads/products', 'public');
            }

            $product->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Product added successfully!',
                    'redirect' => route('vendor.products.index')
                ]);
            }


        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to add product: ' . $e->getMessage()
                ]);
            }
        }
    }


    // Show edit form
    public function edit($id)
    {
        $product = Product::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();
        $categories = DB::table('categories')
            ->where('status', 1)->where('parent_id', 0)
            ->orderBy('name', 'ASC')
            ->get();
        $subCategories = array();
        return view('vendor.products.edit', compact('product', 'categories','subCategories'));
    }

    // Update product
    public function update1(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();

        $request->validate([
            'product_name' => 'required|string|max:200',
            'price' => 'required|numeric|min:0',
            'slug' => 'nullable|string|unique:products,slug,' . $product->id,
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $product->category_id = $request->category_id;
        $product->sub_category_id = $request->sub_category_id;
        $product->product_name = $request->product_name;
        $product->slug = $request->slug ?? Str::slug($request->product_name);
        $product->description = $request->description;
        $product->price = $request->price;
        $product->unit = $request->unit;
        $product->min_order_qty = $request->min_order_qty ?? 1;
        $product->stock_quantity = $request->stock_quantity ?? 0;
        $product->hsn_code = $request->hsn_code;
        $product->gst_rate = $request->gst_rate;

        // Image upload
        if ($request->hasFile('product_image')) {
            $product->product_image = $request->file('product_image')->store('uploads/products', 'public');
        }

        $product->save();

        return redirect()->route('vendor.products.index')->with('success', 'Product updated successfully!');
    }

    public function update(Request $request, $id)
    {
        $product = Product::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();

        try {
            $validatedData = $request->validate([
                'product_name' => 'required|string|max:200',
                'price' => 'required|numeric|min:0',
                'slug' => 'nullable|string|unique:products,slug,' . $product->id,
                'product_image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
                // Add other validation rules as needed
            ]);

            // Update product with validated data
            $product->category_id = $request->category_id;
            $product->sub_category_id = $request->sub_category_id;
            $product->product_name = $validatedData['product_name'];
            $product->slug = $validatedData['slug'] ?? Str::slug($validatedData['product_name']);
            $product->description = $request->description;
            $product->price = $validatedData['price'];
            $product->unit = $request->unit;
            $product->min_order_qty = $request->min_order_qty ?? 1;
            $product->stock_quantity = $request->stock_quantity ?? 0;
            $product->hsn_code = $request->hsn_code;
            $product->gst_rate = $request->gst_rate;

            // Image upload
            if ($request->hasFile('product_image')) {
                $product->product_image = $request->file('product_image')->store('uploads/products', 'public');
            }

            $product->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Product updated successfully!',
                    'redirect' => route('vendor.products.index')
                ]);
            }


        } catch (\Illuminate\Validation\ValidationException $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            throw $e; // Re-throw the exception for non-AJAX requests
        }
    }

    // Show product details
    public function show($id)
    {
        $product = Product::where('id', $id)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        return view('vendor.products.show', compact('product'));
    }

    // Delete product
    public function destroy($id)
    {
        $product = Product::where('id', $id)
            ->where('vendor_id', Auth::id())
            ->firstOrFail();

        try {
            $product->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Product deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete product: ' . $e->getMessage()
            ], 500);
        }
    }
}
