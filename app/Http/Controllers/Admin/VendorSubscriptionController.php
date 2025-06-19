<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\VendorSubscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class VendorSubscriptionController extends Controller
{
    public function index()
    {
        $plans = Plan::where('status', 'active')
            ->where('plan_for', 'vendor')
            ->pluck('name');

        return view('admin.subscriptions.index', compact('plans'));
    }

    public function renderSubscriptionsTable(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $page    = $request->input('page', 1);

        $query = VendorSubscription::with('user')
            ->when($request->vendor, function ($q, $name) {
                $q->whereHas('user', function ($sub) use ($name) {
                    $sub->where('name', 'like', "{$name}%");
                });
            })
            ->when($request->plan, function ($q, $plan) {
                $q->where('plan_name', $plan);
            })
            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {
                $q->where('status', $request->status);
            })
            ->latest();

        $subscriptions = $query->paginate($perPage, ['*'], 'page', $page);

        return view('admin.subscriptions._subscriptions_table', compact('subscriptions'));
    }

    public function create()
    {
        // Do not preload all vendors to avoid loading millions of records.
        // The vendor dropdown will fetch data via AJAX.
        $plans = Plan::where('status', 'active')
            ->where('plan_for', 'vendor')
            ->pluck('name');
        return view('admin.subscriptions.create', compact('plans'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'plan_name' => 'required|exists:plans,name',
            'duration'  => 'required|integer|min:1|max:24',
            'status'    => 'required|in:active,expired',
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
            $data = $validator->validated();
            $duration = (int) $data['duration'];
            $start = Carbon::now();
            $data['start_date'] = $start->toDateString();
            $data['end_date'] = $start->copy()->addMonths($duration)->toDateString();
            unset($data['duration']);
            VendorSubscription::create($data);
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
        $plans = Plan::where('status', 'active')
            ->where('plan_for', 'vendor')
            ->pluck('name');
        $duration = $subscription->start_date && $subscription->end_date
            ? Carbon::parse($subscription->start_date)->diffInMonths(Carbon::parse($subscription->end_date))
            : 1;
        return view('admin.subscriptions.edit', compact('subscription', 'vendor', 'plans', 'duration'));
    }

    public function update(Request $request, $id)
    {
        $subscription = VendorSubscription::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_id'   => 'required|exists:users,id',
            'plan_name' => 'required|exists:plans,name',
            'duration'  => 'required|integer|min:1|max:24',
            'status'    => 'required|in:active,expired',
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
            $data = $validator->validated();
            $duration = (int) $data['duration'];
            $start = Carbon::now();
            $data['start_date'] = $start->toDateString();
            $data['end_date'] = $start->copy()->addMonths($duration)->toDateString();
            unset($data['duration']);
            $subscription->update($data);
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

    public function show($id)
    {
        $subscription = VendorSubscription::with('user')->findOrFail($id);

        $autoPrint = request()->routeIs('admin.vendor-subscriptions.print');

        return view('admin.subscriptions.show', compact('subscription', 'autoPrint'));
    }
}
