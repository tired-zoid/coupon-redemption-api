<?php

use App\Exceptions\Coupon\ActivationLimitReachedException;
use App\Exceptions\Coupon\CouponAlreadyRedeemedException;
use App\Exceptions\Coupon\CouponExpiredException;
use App\Exceptions\Coupon\CouponNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Exceptions\Handler;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->renderable(function (CouponNotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        });
        $exceptions->renderable(function (CouponExpiredException $e) {
            return response()->json(['error' => $e->getMessage()], 422);
        });
        $exceptions->renderable(function (ActivationLimitReachedException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        });
        $exceptions->renderable(function (CouponAlreadyRedeemedException $e) {
            return response()->json(['error' => $e->getMessage()], 409);
        });
    })->create();
