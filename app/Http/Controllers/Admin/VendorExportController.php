<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\VendorExport;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VendorExportController extends Controller
{
    public function index()
    {
        $exports = VendorExport::latest()->paginate(20);
        return view('admin.vendor_exports.index', compact('exports'));
    }

    public function create()
    {
        return view('admin.vendor_exports.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'range_start' => 'required|integer|min:0',
            'range_end'   => 'required|integer|gt:range_start',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json(['status' => 0, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $export = VendorExport::create([
            'range_start' => $request->range_start,
            'range_end'   => $request->range_end,
            'status'      => 'in_progress',
        ]);

        // Run the export command in the background
        $cmd = sprintf('php artisan vendor:export %d > /dev/null 2>&1 &', $export->id);
        exec($cmd);

        if ($request->ajax()) {
            return response()->json([
                'status' => 1,
                'message' => 'Export started.',
                'redirect' => route('admin.vendor-exports.index'),
            ]);
        }

        return redirect()->route('admin.vendor-exports.index')
            ->with('success', 'Export started.');
    }

    public function download($id)
    {
        $export = VendorExport::findOrFail($id);
        $filePath = public_path('uploads/report/vendor/' . $export->file_name);

        if (! file_exists($filePath)) {
            abort(404);
        }

        return response()->download($filePath);
    }

    public function destroy($id)
    {
        $export = VendorExport::findOrFail($id);

        if ($export->file_name) {
            $path = public_path('uploads/report/vendor/' . $export->file_name);
            if (file_exists($path)) {
                unlink($path);
            }
        }

        $export->delete();

        return redirect()->route('admin.vendor-exports.index')
            ->with('success', 'Export deleted.');
    }
}
