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
        'name',
        'email',
        'phone',
        'country',
        'state',
        'city',
        'pincode',
        'address',
    ];

    /**
     * Get the user that owns the buyer profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
