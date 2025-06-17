<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Str;
use Yajra\DataTables\DataTables;
use Auth;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    // Show vendor's all categories
    public function index()
    {
        return view('admin.categories.index');
    }

    // Fetch categories for DataTable
    public function getCategories_old(Request $request)
    {
        $categories = Category::with('parent')
            ->orderBy('name', 'asc')
            ->latest();

        return DataTables::of($categories)
            ->addIndexColumn()
            ->editColumn('status', function ($category) {
                $status = $category->status ? 'active' : 'inactive';
                $class = $status === 'active'
                    ? 'badge border border-success text-success px-2 py-1 fs-13'
                    : 'badge border border-danger text-danger px-2 py-1 fs-13';

                return '<span class="'. $class .'">'. ucfirst($status) .'</span>';
            })
            ->addColumn('parent', function ($category) {
                return $category->parent ? $category->parent->name : 'Main Category';
            })
            ->addColumn('action', function ($category) {
                return '<a href="' . route('admin.categories.edit', $category->id) . '" class="btn btn-soft-primary btn-sm"><iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon></a> <button class="btn btn-soft-danger btn-sm delete-category" data-id="'.$category->id.'"><iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon></button>';
            })
            ->rawColumns(['status', 'action', 'parent'])
            ->make(true);
    }

    public function getCategories(Request $request)
    {

        // Start query
        $categories = Category::with('parent')
            ->when($request->category_name, function ($query, $categoryName) {
                $query->where('name', 'like', '%' . $categoryName . '%');
            })
            ->when($request->status !== null && $request->status !== '', function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('name', 'asc');

        // Return DataTables response
        return DataTables::of($categories)
            ->addIndexColumn()
            ->editColumn('status', function ($category) {
                $status = $category->status ? 'active' : 'inactive';
                $icon = $category->status
                    ? '<i class="bi bi-check-circle me-1 text-success"></i>'
                    : '<i class="bi bi-x-circle me-1 text-danger"></i>';
                $class = $category->status
                    ? 'badge border border-success text-success px-2 py-1 fs-13'
                    : 'badge border border-danger text-danger px-2 py-1 fs-13';

                return '<span class="' . $class . '">' . $icon . ucfirst($status) . '</span>';
            })
            ->addColumn('parent', function ($category) {
                return $category->parent ? $category->parent->name : 'Main Category';
            })
            ->addColumn('action', function ($category) {
                return '<a href="' . route('admin.categories.edit', $category->id) . '" class="btn btn-soft-primary btn-sm">
                            <iconify-icon icon="solar:pen-2-broken" class="align-middle fs-18"></iconify-icon>
                        </a>
                        <button class="btn btn-soft-danger btn-sm delete-category" data-id="' . $category->id . '">
                            <iconify-icon icon="solar:trash-bin-minimalistic-2-broken" class="align-middle fs-18"></iconify-icon>
                        </button>';
            })
            ->rawColumns(['status', 'action', 'parent'])
            ->make(true);
    }


    // Show create category form
    public function create()
    {
        // Fetch main categories (parent_id = 0)
        $mainCategories = Category::where('parent_id', 0)
            ->where('status', 1)
            ->orderBy('name', 'ASC')
            ->get();

        return view('admin.categories.create', compact('mainCategories'));
    }

    // Save category
    public function store(Request $request)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|unique:categories,slug',
            'parent_id' => 'nullable|exists:categories,id',
            'status' => 'required|boolean',
        ]);

        // Check if validation fails
        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['status' => 0, 'message' => $validator->errors()->first()]);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $category = new Category();

            // Sanitize input data
            $name = strip_tags(trim($request->name));
            $slug = $request->slug ? strip_tags(trim($request->slug)) : Str::slug($name);

            // Set category properties
            $category->name = $name;
            $category->slug = $slug;
            $category->parent_id = $request->parent_id ?? 0;
            $category->status = $request->status;

            $category->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Category added successfully!',
                    'redirect' => route('admin.categories.index')
                ]);
            }



        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to add category: ' . $e->getMessage()
                ]);
            }

        }
    }

    // Show edit form
    public function edit($id)
    {
        $category = Category::where('id', $id)->firstOrFail();
        $mainCategories = Category::where('parent_id', 0)
            ->where('status', 1)
            ->where('id', '!=', $id) // Exclude current category from parent options
            ->orderBy('name', 'ASC')
            ->get();

        return view('admin.categories.edit', compact('category', 'mainCategories'));
    }

    // Update category
    public function update(Request $request, $id)
    {
        $category = Category::where('id', $id)->firstOrFail();

        try {
            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'slug' => 'nullable|string|unique:categories,slug,' . $category->id,
                'parent_id' => 'nullable|exists:categories,id',
                'status' => 'required|boolean',
            ]);

            // Sanitize input data
            $name = strip_tags(trim($request->name));
            $slug = $request->slug ? strip_tags(trim($request->slug)) : Str::slug($name);

            // Update category with validated data
            $category->name = $name;
            $category->slug = $slug;
            $category->parent_id = $request->parent_id ?? 0;
            $category->status = $request->status;

            $category->save();

            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Category updated successfully!',
                    'redirect' => route('admin.categories.index')
                ]);
            }


        } catch (\Illuminate\Validation\ValidationException $e) {
             if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to update category: ' . $e->getMessage()
                ]);
            }
        }
    }

    // Delete category
    public function destroy($id)
    {
        $category = Category::where('id', $id)->where('vendor_id', Auth::id())->firstOrFail();

        try {
            // Check if category has children
            if (Category::where('parent_id', $id)->exists()) {
                throw new \Exception('Cannot delete category because it has subcategories.');
            }

            $category->delete();

            return response()->json([
                'status' => 1,
                'message' => 'Category deleted successfully!'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 0,
                'message' => 'Failed to delete category: ' . $e->getMessage()
            ], 500);
        }
    }
}