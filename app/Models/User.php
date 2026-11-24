<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens; 

#[Fillable(['name', 'email', 'password','status','role','is_active'])]
#[Hidden(['password', 'remember_token','farm_id'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable,HasApiTokens;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return 
        [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    // farm
    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }
    // farmProducts
    public function farmProducts()
    {
        return $this->hasMany(FarmProduct::class);
    }

     // sales
    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

}

