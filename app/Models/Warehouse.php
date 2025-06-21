<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\WarehouseProduct;
use App\Models\Product;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'name',
        'address',
        'city',
        'state',
        'pincode',
    ];

    public function vendor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function productStocks(): HasMany
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'warehouse_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }
}
