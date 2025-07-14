<?php

namespace App\Http\Controllers\Buyer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\BuyRequirement;
use Carbon\Carbon;

class BuyRequirementController extends Controller
{
    public function create()
    {
        return view('buyer.post_buy');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_name'  => 'required|string|max:255',
            'country_code'  => 'required|string|max:5',
            'mobile_number' => 'required|string|max:20',
            'expected_date' => 'nullable|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'status'  => 0,
                    'message' => $validator->errors()->first(),
                    'errors'  => $validator->errors(),
                ], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = $validator->validated();
        if (!empty($data['expected_date'])) {
            $data['expected_date'] = Carbon::createFromFormat('d-m-Y', $data['expected_date'])->format('Y-m-d');
        }

        BuyRequirement::create($data);

        if ($request->ajax()) {
            return response()->json([
                'status'  => 1,
                'message' => 'Requirement submitted successfully!',
            ]);
        }

        return redirect()->back()->with('success', 'Requirement submitted successfully!');
    }
}
