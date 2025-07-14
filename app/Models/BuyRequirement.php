<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BuyRequirement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_name',
        'country_code',
        'mobile_number',
        'expected_date',
    ];

    protected $casts = [
        'expected_date' => 'date',
    ];
}
