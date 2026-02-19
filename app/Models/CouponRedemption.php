<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponRedemption extends Model
{
    protected $table = 'coupon_redemptions';

    protected $fillable = [
        'user_id',
        'coupon_id',
        'redeemed_at'
    ];
}
