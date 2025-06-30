<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NewsletterController extends Controller
{
    /**
     * Handle newsletter subscriptions via AJAX.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'subscribe_date' => 'required|date_format:d-m-Y',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Here you would normally persist the subscription or trigger other logic.

        return response()->json(['success' => true, 'message' => 'Subscription successful.']);
    }
}
