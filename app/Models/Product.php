<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $fillable = [
        'name',
        'description',
        'unit',
        'is_active'
    ];
    // farmProducts
    public function farmProducts()
    {
        return $this->hasMany(FarmProduct::class);
    }
}
