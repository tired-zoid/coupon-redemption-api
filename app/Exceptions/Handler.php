<?php

namespace App\Exceptions;

use App\Exceptions\Coupon\ActivationLimitReachedException;
use App\Exceptions\Coupon\CouponAlreadyRedeemedException;
use App\Exceptions\Coupon\CouponExpiredException;
use App\Exceptions\Coupon\CouponNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (CouponNotFoundException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 404);
            }
        });

        $this->renderable(function (CouponExpiredException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 422);
            }
        });

        $this->renderable(function (ActivationLimitReachedException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 409);
            }
        });

        $this->renderable(function (CouponAlreadyRedeemedException $e, $request) {
            if ($request->expectsJson() || $request->is('api/*')) {
                return response()->json(['error' => $e->getMessage()], 409);
            }
        });
    }
}
