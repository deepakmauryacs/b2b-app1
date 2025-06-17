<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\HasOne; // Import HasOne


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

    public function products()
    {
        return $this->hasMany(\App\Models\Product::class, 'vendor_id');
    }

}
