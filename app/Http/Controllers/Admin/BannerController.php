<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BannerController extends Controller
{
    public function index()
    {
        return view('admin.banners.index');
    }

    public function getBanners(Request $request)
    {
        $banners = Banner::query()->latest();

        return DataTables::of($banners)
            ->addIndexColumn()
            ->editColumn('status', function ($banner) {
                $status = $banner->status == 1 ? 'active' : 'inactive';
                $class = $banner->status == 1 ? 'badge border border-success text-success px-2 py-1 fs-13' : 'badge border border-danger text-danger px-2 py-1 fs-13';
                return '<span class="'.$class.'">'.ucfirst($status).'</span>';
            })
            ->addColumn('action', function ($banner) {
                return '<a href="'.route('admin.banners.edit', $banner->id).'" class="btn btn-soft-primary btn-sm"><i class="bi bi-pencil"></i></a>';
            })
            ->rawColumns(['status','action'])
            ->make(true);
    }

    /**
     * Render banners table for AJAX pagination similar to other lists.
     */
    public function renderBannersTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);

        $query = Banner::query()
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('status', (int) $request->status);
            })
            ->orderBy('created_at', 'desc');

        $banners = $query->paginate($perPage, ['*'], 'page', $page);

        return view('admin.banners._banners_table', compact('banners'));
    }

    public function create()
    {
        return view('admin.banners.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'banner_img' => 'required|string|max:255',
            'banner_link' => 'nullable|url',
            'banner_start_date' => 'nullable|date_format:d-m-Y',
            'banner_end_date' => 'nullable|date_format:d-m-Y|after_or_equal:banner_start_date',
            'status' => 'required|in:1,2',
            'banner_type' => 'required|integer'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['banner_start_date'])) {
            $data['banner_start_date'] = Carbon::createFromFormat('d-m-Y', $data['banner_start_date'])->format('Y-m-d');
        }
        if (!empty($data['banner_end_date'])) {
            $data['banner_end_date'] = Carbon::createFromFormat('d-m-Y', $data['banner_end_date'])->format('Y-m-d');
        }

        Banner::create($data);

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => 'Banner added successfully!',
                'redirect' => route('admin.banners.index'),
            ]);
        }
    }

    public function edit($id)
    {
        $banner = Banner::findOrFail($id);
        return view('admin.banners.edit', compact('banner'));
    }

    public function update(Request $request, $id)
    {
        $banner = Banner::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'banner_img' => 'required|string|max:255',
            'banner_link' => 'nullable|url',
            'banner_start_date' => 'nullable|date_format:d-m-Y',
            'banner_end_date' => 'nullable|date_format:d-m-Y|after_or_equal:banner_start_date',
            'status' => 'required|in:1,2',
            'banner_type' => 'required|integer'
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['banner_start_date'])) {
            $data['banner_start_date'] = Carbon::createFromFormat('d-m-Y', $data['banner_start_date'])->format('Y-m-d');
        }
        if (!empty($data['banner_end_date'])) {
            $data['banner_end_date'] = Carbon::createFromFormat('d-m-Y', $data['banner_end_date'])->format('Y-m-d');
        }

        $banner->update($data);

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => 'Banner updated successfully!',
                'redirect' => route('admin.banners.index'),
            ]);
        }
    }
}
