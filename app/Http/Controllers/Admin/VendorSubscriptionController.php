<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorSubscription;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class VendorSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = VendorSubscription::with('user')->latest()->paginate(10);
        return view('admin.subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        // Do not preload all vendors to avoid loading millions of records.
        // The vendor dropdown will fetch data via AJAX.
        return view('admin.subscriptions.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'plan_name'  => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:active,expired',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            VendorSubscription::create($validator->validated());
            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Subscription added successfully!',
                    'redirect' => route('admin.vendor-subscriptions.index'),
                ]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to add subscription: ' . $e->getMessage(),
                ], 500);
            }
        }

        return redirect()->route('admin.vendor-subscriptions.index')
            ->with('success', 'Subscription added successfully!');
    }

    public function edit($id)
    {
        $subscription = VendorSubscription::with('user:id,name')->findOrFail($id);
        // Pass only the current vendor to the view for the preselected option.
        $vendor = $subscription->user;
        return view('admin.subscriptions.edit', compact('subscription', 'vendor'));
    }

    public function update(Request $request, $id)
    {
        $subscription = VendorSubscription::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id'    => 'required|exists:users,id',
            'plan_name'  => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
            'status'     => 'required|in:active,expired',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'errors' => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $subscription->update($validator->validated());
            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Subscription updated successfully!',
                    'redirect' => route('admin.vendor-subscriptions.index'),
                ]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to update subscription: ' . $e->getMessage(),
                ], 500);
            }
        }

        return redirect()->route('admin.vendor-subscriptions.index')
            ->with('success', 'Subscription updated successfully!');
    }
}
