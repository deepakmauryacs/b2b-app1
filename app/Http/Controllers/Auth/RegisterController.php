<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Validation\ValidationException;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.registration');
    }

    public function register(Request $request)
    {
        try {
            // Validate user input
            $validatedData = $request->validate([
                'name' => 'required|string|max:100',
                'email' => 'required|email|unique:users,email',
                'phone' => 'nullable|string|max:15',
                'role' => 'required|in:vendor,buyer',
                'password' => 'required|string|min:6|confirmed',
                'captcha' => 'required|numeric',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'status' => false,
                'message' => 'Validation failed. Please check your input.',
                'errors' => $e->validator->errors()
            ], 422);
        }

        // Verify math CAPTCHA
        if ((int) $request->captcha !== session('captcha_result')) {
            return response()->json([
                'status' => false,
                'message' => 'Incorrect CAPTCHA answer.',
                'errors' => ['captcha' => ['Incorrect CAPTCHA answer.']]
            ], 422);
        }

        // Create user
        $user = User::create([
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'phone' => $validatedData['phone'],
            'role' => $validatedData['role'],
            'password' => Hash::make($validatedData['password']),
        ]);

        // If vendor, create vendor profile
        if ($user->role === 'vendor') {
            \App\Models\VendorProfile::create([
                'user_id' => $user->id,
                'email'   => $user->email,
                'phone'   => $user->phone,
            ]);
        }


        // Log in the user
        Auth::login($user);

        // Determine redirect route based on user role
        if ($user->role === 'vendor') {
            $redirect = route('vendor.dashboard');
        } elseif ($user->role === 'buyer') {
            $redirect = route('buyer.dashboard');
        } else {
            $redirect = route('home');
        }

        return response()->json([
            'status' => true,
            'message' => 'Registration successful!',
            'redirect' => $redirect
        ]);
    }


}
