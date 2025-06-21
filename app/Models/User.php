<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasOne; // Import HasOne
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\BuyerProfile; // Import BuyerProfile
use App\Models\Role;
use App\Models\VendorSubscription;
use App\Models\Warehouse;


class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'role',
        'status',
        'is_profile_verified',
    ];


    // use LogsActivity;

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logFillable() 
    //         ->logOnlyDirty()
    //         ->dontSubmitEmptyLogs();
    // }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // app/Models/User.php
    public function vendorProfile(): HasOne
    {
        return $this->hasOne(VendorProfile::class);
        // Assuming you have a VendorProfile model and a foreign key 'user_id'
        // in your vendor_profiles table linking back to the users table.
    }

    /**
     * Get the buyer profile associated with the user.
     */
    public function buyerProfile(): HasOne
    {
        return $this->hasOne(BuyerProfile::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(VendorSubscription::class);
    }

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'vendor_id');
    }

    public function warehouses()
    {
        return $this->hasMany(Warehouse::class, 'vendor_id');
    }

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    public function hasPermission(string $module, string $action): bool
    {
        $action = strtolower($action);
        if (!in_array($action, ['add','edit','view','export'])) {
            return false;
        }

        return $this->roles()->whereHas('permissions', function ($q) use ($module, $action) {
            $q->where('module', $module)->where("can_${action}", true);
        })->exists();
    }
}
