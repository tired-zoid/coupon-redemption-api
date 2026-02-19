<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'code',
        'activations_limit',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];
}
