<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Farm extends Model
{
    //
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'status'
    ];
    // user
    public function user()
    {
        return $this->hasMany(User::class);
    }
    // sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }
}
