<?php

namespace App\Actions;

use App\Models\Coupon;
use App\Models\User;
use App\Models\CouponRedemption;
use App\Exceptions\Coupon\CouponNotFoundException;
use App\Exceptions\Coupon\CouponAlreadyRedeemedException;
use App\Exceptions\Coupon\CouponExpiredException;
use App\Exceptions\Coupon\ActivationLimitReachedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RedeemCouponAction
{
    public function execute(string $code, string $externalUserId): void
    {
        $user = User::firstOrCreate(
            ['external_id' => $externalUserId],
            [
                'name' => "User {$externalUserId}",
                'email' => "user.{$externalUserId}@example.com"
            ]
        );

        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            throw new CouponNotFoundException();
        }

        DB::transaction(function () use ($user, $coupon) {
            $lockedCoupon = Coupon::where('id', $coupon->id)
                ->lockForUpdate()
                ->first();

            if ($lockedCoupon->expires_at && now()->gt($lockedCoupon->expires_at)) {
                throw new CouponExpiredException();
            }

            if ($lockedCoupon->activations_count >= $lockedCoupon->activations_limit) {
                throw new ActivationLimitReachedException();
            }

            $alreadyRedeemed = CouponRedemption::where('user_id', $user->id)
                ->where('coupon_id', $lockedCoupon->id)
                ->exists();

            if ($alreadyRedeemed) {
                throw new CouponAlreadyRedeemedException();
            }

            CouponRedemption::create([
                'user_id' => $user->id,
                'coupon_id' => $lockedCoupon->id,
                'redeemed_at' => now()
            ]);

            $lockedCoupon->increment('activations_count');
        });
    }
}
