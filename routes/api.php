<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\CouponController;

Route::post('/coupons', [CouponController::class, 'store']);
Route::get('/coupons', [CouponController::class, 'index']);
Route::post('/coupons/{code}/redeem', [CouponController::class, 'redeem']);
