<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
   
    protected $fillable = [
        'user_id',
        'farm_id',
        'customer_name',
        'customer_phone',
        'total_amount',
        'status'
    ];

    // user 
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    // farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
}
