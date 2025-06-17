<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;

class VendorDashboardController extends Controller
{
    public function index()
    {
        return view('vendor.dashboard');
    }
}