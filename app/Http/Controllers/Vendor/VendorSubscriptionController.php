<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\VendorSubscription;
use App\Models\Plan;
use Carbon\Carbon;

class VendorSubscriptionController extends Controller
{
    public function index()
    {
        $subscription = auth()->user()->subscription;
        $plans = Plan::where('status', 'active')
            ->where('plan_for', 'vendor')
            ->pluck('name');
        $duration = $subscription && $subscription->start_date && $subscription->end_date
            ? Carbon::parse($subscription->start_date)->diffInMonths(Carbon::parse($subscription->end_date))
            : 1;
        return view('vendor.subscriptions.index', compact('subscription', 'plans', 'duration'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_name' => 'required|exists:plans,name',
            'duration'  => 'required|integer|min:1|max:24',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
        $duration = (int) $data['duration'];
        $start = Carbon::now();
        $data['start_date'] = $start->toDateString();
        $data['end_date'] = $start->copy()->addMonths($duration)->toDateString();
        unset($data['duration']);
        $user = Auth::user();
        $subscription = $user->subscription;

        if ($subscription) {
            $subscription->update($data);
        } else {
            $data['user_id'] = $user->id;
            VendorSubscription::create($data);
        }

        return response()->json([
            'status' => true,
            'message' => 'Subscription saved successfully!',
            'reload' => true,
        ]);
    }

    public function invoice()
    {
        $subscription = auth()->user()->subscription;
        if (!$subscription) {
            abort(404);
        }

        return view('vendor.subscriptions.invoice', compact('subscription'));
    }
}
