<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\VendorSubscription;

class VendorSubscriptionController extends Controller
{
    public function index()
    {
        $subscription = auth()->user()->subscription;
        return view('vendor.subscriptions.index', compact('subscription'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'plan_name'  => 'required|string|max:100',
            'start_date' => 'required|date',
            'end_date'   => 'required|date|after_or_equal:start_date',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed.',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = $validator->validated();
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
}
