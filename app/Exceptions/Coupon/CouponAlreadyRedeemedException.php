<?php
namespace App\Exceptions\Coupon;

use Exception;

class CouponAlreadyRedeemedException extends Exception
{
    protected $message = 'Coupon already redeemed';
}
