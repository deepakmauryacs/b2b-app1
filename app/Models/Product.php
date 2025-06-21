<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Warehouse;
use App\Models\WarehouseProduct;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{
    protected $fillable = [
        'vendor_id',
        'category_id',
        'sub_category_id',
        'product_name',
        'product_image',
        'slug',
        'description',
        'price',
        'unit',
        'min_order_qty',
        'stock_quantity',
        'hsn_code',
        'gst_rate',
        'status',
    ];

    // Relationships
    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function stockLogs(): HasMany
    {
        return $this->hasMany(StockLog::class);
    }

    public function latestStockLog()
    {
        return $this->hasOne(StockLog::class)->latestOfMany();
    }

    public function warehouseStocks(): HasMany
    {
        return $this->hasMany(WarehouseProduct::class);
    }

    public function warehouses(): BelongsToMany
    {
        return $this->belongsToMany(Warehouse::class, 'warehouse_product')
                    ->withPivot('quantity')
                    ->withTimestamps();
    }

    public function stockInWarehouse(int $warehouseId): int
    {
        $stock = $this->warehouseStocks()->where('warehouse_id', $warehouseId)->first();
        return $stock ? $stock->quantity : 0;
    }
}
