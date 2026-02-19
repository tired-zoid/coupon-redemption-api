<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateCouponRequest;
use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Actions\RedeemCouponAction;

class CouponController extends Controller
{
    public function store(CreateCouponRequest $request)
    {
        $coupon = Coupon::create($request->validated());

        return response()->json([
            'id' => $coupon->id,
            'code' => $coupon->code,
        ], 201);
    }

    public function index()
    {
        return Coupon::all();
    }

    public function redeem(string $code, Request $request, RedeemCouponAction $action)
    {
        $externalUserId = $request->header('X-User-Id');

        if (!$externalUserId) {
            return response()->json(['error' => 'User ID header missing'], 400);
        }

        $action->execute($code, $externalUserId);

        return response()->json(['success' => true]);
    }
}
