<?php

namespace App\Exports;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

class VendorsExport implements FromCollection, WithHeadings, WithMapping, WithStrictNullComparison
{
    public function collection()
    {
        return User::where('role', 'vendor')
            ->leftJoin('vendor_profiles', 'users.id', '=', 'vendor_profiles.user_id')
            ->select([
                'users.id',
                'users.name',
                'users.email',
                'users.phone',
                'users.status',
                'users.is_profile_verified',
                'vendor_profiles.store_name',
                'vendor_profiles.gst_no',
                'vendor_profiles.pincode',
                'vendor_profiles.address',
                'vendor_profiles.country',
                'vendor_profiles.state',
                'vendor_profiles.city',
                DB::raw('(SELECT COUNT(*) FROM products WHERE vendor_id = users.id AND status = "approved") as approved_products_count'),
                DB::raw('(SELECT COUNT(*) FROM products WHERE vendor_id = users.id AND status = "pending") as pending_products_count'),
                'users.created_at'
            ])
            ->orderBy('users.name', 'asc')
            ->get();
    }

    public function headings(): array
    {
        return [
            'ID',
            'Name',
            'Email',
            'Phone',
            'Status',
            'Profile Verified',
            'Store Name',
            'GST No',
            'Pincode',
            'Address',
            'Country',
            'State',
            'City',
            'Approved Products',
            'Pending Products',
            'Created At'
        ];
    }

    public function map($vendor): array
    {
        $mapped = [
            (int) $vendor->id,
            (string) $vendor->name,
            (string) $vendor->email,
            (string) $vendor->phone,
            (string) ($vendor->status == '1' ? 'Active' : 'Inactive'),
            (string) ($vendor->is_profile_verified == '1' ? 'Verified' : 'Not Verified'),
            (string) $vendor->store_name,
            (string) $vendor->gst_no,
            (string) $vendor->pincode,
            (string) $vendor->address,
            (string) $vendor->country,
            (string) $vendor->state,
            (string) $vendor->city,
            (int) $vendor->approved_products_count,
            (int) $vendor->pending_products_count,
            optional($vendor->created_at)->format('Y-m-d H:i:s'),
        ];

        // Optional: log types and values for debugging
        foreach ($mapped as $index => $value) {
            Log::info("VendorsExport: Column $index => " . json_encode($value) . " | Type: " . gettype($value));
        }

        return $mapped;
    }
}
