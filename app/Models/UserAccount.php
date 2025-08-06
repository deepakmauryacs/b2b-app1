<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'user_type',
        'gender',
        'gst_no',
        'otp',
        'latitude',
        'longitude',
        'status',
        'referral_code',
        'referral_by',
        'is_verified',
        'product_and_services',
        'parent_id',
    ];

    protected $hidden = [
        'password',
    ];
}
