<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Models\HelpSupport;
use Carbon\Carbon;

class VendorHelpSupportController extends Controller
{
    public function index()
    {
        return view('vendor.help_support.index', [
            'pageTitle' => 'Help & Support List'
        ]);
    }

    public function renderHelpsTable(Request $request)
    {
        $query = HelpSupport::where('created_by', Auth::id());

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $helps = $query->orderBy('created_at', 'desc')
                        ->paginate($request->input('per_page', 10));

        return view('vendor.help_support._table', compact('helps'));
    }

    public function create()
    {
        return view('vendor.help_support.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'contact_no' => 'required|string|max:20',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $data['user_type'] = 'vendor';
        $data['status'] = 'open';
        $data['created_by'] = Auth::id();
        $data['updated_by'] = Auth::id();

        if ($request->hasFile('attachment')) {
            $path = $request->file('attachment')->store('uploads/help_support', 'public');
            $data['attachment'] = $path;
        }

        HelpSupport::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Request submitted successfully!',
            'redirect' => route('vendor.help-support.index'),
        ]);
    }

    public function show($id)
    {
        $help = HelpSupport::where('id', $id)
            ->where('created_by', Auth::id())
            ->firstOrFail();

        return view('vendor.help_support.show', compact('help'));
    }
}
