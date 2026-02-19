<?php
namespace App\Exceptions\Coupon;

use Exception;

class ActivationLimitReachedException extends Exception
{
    protected $message = 'Activation limit reached';
}
