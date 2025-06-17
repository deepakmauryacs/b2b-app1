<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BuyerProfile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'store_name',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'pincode',
        'address',
        'gst_no',
        'gst_doc',
        'store_logo',
    ];

    /**
     * Get the user that owns the buyer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
