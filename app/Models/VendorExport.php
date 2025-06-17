<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VendorExport extends Model
{
    protected $fillable = [
        'range_start',
        'range_end',
        'status',
        'file_name',
    ];
}
