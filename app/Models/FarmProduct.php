<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FarmProduct extends Model
{
    //
    protected $fillable = [
        'farm_id',
        'product_id',
        'selling_price',
        'stock_quantity',
        'is_active',
    ];
    // product
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
