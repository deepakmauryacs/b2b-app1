<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\BuyerSubscription;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class BuyerSubscriptionController extends Controller
{
    public function index()
    {
        $subscriptions = BuyerSubscription::with('user')->latest()->paginate(10);
        return view('admin.buyer_subscriptions.index', compact('subscriptions'));
    }

    public function create()
    {
        $plans = Plan::where('status', 'active')
            ->where('plan_for', 'buyer')
            ->pluck('name');
        return view('admin.buyer_subscriptions.create', compact('plans'));
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
            BuyerSubscription::create($data);
            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Subscription added successfully!',
                    'redirect' => route('admin.buyer-subscriptions.index'),
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

        return redirect()->route('admin.buyer-subscriptions.index')
            ->with('success', 'Subscription added successfully!');
    }

    public function edit($id)
    {
        $subscription = BuyerSubscription::with('user:id,name')->findOrFail($id);
        $buyer = $subscription->user;
        $plans = Plan::where('status', 'active')
            ->where('plan_for', 'buyer')
            ->pluck('name');
        $duration = $subscription->start_date && $subscription->end_date
            ? Carbon::parse($subscription->start_date)->diffInMonths(Carbon::parse($subscription->end_date))
            : 1;
        return view('admin.buyer_subscriptions.edit', compact('subscription', 'buyer', 'plans', 'duration'));
    }

    public function update(Request $request, $id)
    {
        $subscription = BuyerSubscription::findOrFail($id);

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
                    'redirect' => route('admin.buyer-subscriptions.index'),
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

        return redirect()->route('admin.buyer-subscriptions.index')
            ->with('success', 'Subscription updated successfully!');
    }
}
