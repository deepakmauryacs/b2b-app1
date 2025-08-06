<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserAccount;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserAccountController extends Controller
{
    public function index()
    {
        $accounts = UserAccount::orderBy('created_at', 'desc')->get();
        return view('admin.user_accounts.index', compact('accounts'));
    }

    public function create()
    {
        return view('admin.user_accounts.create');
    }

    public function store(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:user_accounts,email',
            'phone' => 'required|string|max:20|unique:user_accounts,phone',
            'password' => 'required|string|min:8',
            'user_type' => 'required|string|max:50',
            'gender' => 'nullable|string|max:20',
            'gst_no' => 'nullable|string|max:30',
            'otp' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => ['required', Rule::in(['1','2'])],
            'referral_code' => 'nullable|string|max:20',
            'referral_by' => 'nullable|string|max:20',
            'is_verified' => ['required', Rule::in(['1','2'])],
            'product_and_services' => 'nullable|string',
            'parent_id' => 'nullable|integer',
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

        $data = $validator->validated();
        $data['password'] = bcrypt($data['password']);

        try {
            UserAccount::create($data);
            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'User account created successfully!',
                    'redirect' => route('admin.user-accounts.index'),
                ]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to create user account: ' . $e->getMessage(),
                ], 500);
            }
        }

        return redirect()->route('admin.user-accounts.index')
            ->with('success', 'User account created successfully!');
    }

    public function edit($id)
    {
        $account = UserAccount::findOrFail($id);
        return view('admin.user_accounts.edit', compact('account'));
    }

    public function update(Request $request, $id)
    {
        $account = UserAccount::findOrFail($id);

        $validator = \Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => ['required','email',Rule::unique('user_accounts')->ignore($account->id)],
            'phone' => ['required','string','max:20',Rule::unique('user_accounts','phone')->ignore($account->id)],
            'password' => 'nullable|string|min:8',
            'user_type' => 'required|string|max:50',
            'gender' => 'nullable|string|max:20',
            'gst_no' => 'nullable|string|max:30',
            'otp' => 'nullable|string|max:10',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'status' => ['required', Rule::in(['1','2'])],
            'referral_code' => 'nullable|string|max:20',
            'referral_by' => 'nullable|string|max:20',
            'is_verified' => ['required', Rule::in(['1','2'])],
            'product_and_services' => 'nullable|string',
            'parent_id' => 'nullable|integer',
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

        $data = $validator->validated();
        if (!empty($data['password'])) {
            $data['password'] = bcrypt($data['password']);
        } else {
            unset($data['password']);
        }

        try {
            $account->update($data);
            if ($request->ajax()) {
                return response()->json([
                    'status' => 1,
                    'message' => 'User account updated successfully!',
                    'redirect' => route('admin.user-accounts.index'),
                ]);
            }
        } catch (\Exception $e) {
            if ($request->ajax()) {
                return response()->json([
                    'status' => 0,
                    'message' => 'Failed to update user account: ' . $e->getMessage(),
                ], 500);
            }
        }

        return redirect()->route('admin.user-accounts.index')
            ->with('success', 'User account updated successfully!');
    }
}
