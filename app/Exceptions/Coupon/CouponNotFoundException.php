<?php
namespace App\Exceptions\Coupon;

use Exception;

class CouponNotFoundException extends Exception
{
    protected $message = 'Coupon not found';
}
