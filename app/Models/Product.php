<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
{   
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
        'status', // Add this line
        // ... any other fields you need to mass assign
    ];

    // use LogsActivity;

    // public function getActivitylogOptions(): LogOptions
    // {
    //     return LogOptions::defaults()
    //         ->logFillable() 
    //         ->logOnlyDirty()
    //         ->dontSubmitEmptyLogs();
    // }


    public function vendor()
    {
        return $this->belongsTo(User::class, 'vendor_id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function stockLogs()
    {
        return $this->hasMany(StockLog::class);
    }

}
