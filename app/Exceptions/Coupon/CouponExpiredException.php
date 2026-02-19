<?php
namespace App\Exceptions\Coupon;

use Exception;

class CouponExpiredException extends Exception
{
    protected $message = 'Coupon expired';
}
