<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Coupon;
use App\Models\CouponRedemption;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CouponRedemptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_redeem_coupon()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'external_id' => 'ext_123'
        ]);

        $coupon = Coupon::create([
            'code' => 'WELCOME100',
            'activations_limit' => 5,
            'activations_count' => 0,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->withHeader('X-User-Id', $user->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('coupon_redemptions', [
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
        ]);
    }

    public function test_user_cannot_redeem_same_coupon_twice()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test2@example.com',
            'external_id' => 'ext_456'
        ]);

        $coupon = Coupon::create([
            'code' => 'WELCOME101',
            'activations_limit' => 5,
            'activations_count' => 0,
            'expires_at' => now()->addDay(),
        ]);

        $this->withHeader('X-User-Id', $user->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response = $this->withHeader('X-User-Id', $user->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response->assertStatus(409)
            ->assertJson(['error' => 'Coupon already redeemed']);
    }

    public function test_cannot_redeem_expired_coupon()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test3@example.com',
            'external_id' => 'ext_789'
        ]);

        $coupon = Coupon::create([
            'code' => 'EXPIRED100',
            'activations_limit' => 5,
            'activations_count' => 0,
            'expires_at' => now()->subDay(),
        ]);

        $response = $this->withHeader('X-User-Id', $user->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response->assertStatus(422)
            ->assertJson(['error' => 'Coupon expired']);
    }

    public function test_cannot_redeem_coupon_if_limit_reached()
    {
        $user1 = User::create([
            'name' => 'User 1',
            'email' => 'user1@example.com',
            'external_id' => 'ext_111'
        ]);

        $user2 = User::create([
            'name' => 'User 2',
            'email' => 'user2@example.com',
            'external_id' => 'ext_222'
        ]);

        $user3 = User::create([
            'name' => 'User 3',
            'email' => 'user3@example.com',
            'external_id' => 'ext_333'
        ]);

        $coupon = Coupon::create([
            'code' => 'LIMIT100',
            'activations_limit' => 2,
            'activations_count' => 0,
            'expires_at' => now()->addDay(),
        ]);

        $this->withHeader('X-User-Id', $user1->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $this->withHeader('X-User-Id', $user2->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response = $this->withHeader('X-User-Id', $user3->external_id)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response->assertStatus(409)
            ->assertJson(['error' => 'Activation limit reached']);
    }

    /**
     * Дополнительный тест: пользователь создается автоматически
     */
    public function test_user_is_created_automatically_if_not_exists()
    {
        $externalId = 'new_user_123';

        $coupon = Coupon::create([
            'code' => 'AUTO100',
            'activations_limit' => 5,
            'activations_count' => 0,
            'expires_at' => now()->addDay(),
        ]);

        $response = $this->withHeader('X-User-Id', $externalId)
            ->postJson("/api/coupons/{$coupon->code}/redeem");

        $response->assertStatus(200)
            ->assertJson(['success' => true]);

        $this->assertDatabaseHas('users', [
            'external_id' => $externalId,
        ]);

        $user = User::where('external_id', $externalId)->first();

        $this->assertDatabaseHas('coupon_redemptions', [
            'user_id' => $user->id,
            'coupon_id' => $coupon->id,
        ]);
    }

    /**
     * Дополнительный тест: несуществующий промокод
     */
    public function test_cannot_redeem_nonexistent_coupon()
    {
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test4@example.com',
            'external_id' => 'ext_999'
        ]);

        $response = $this->withHeader('X-User-Id', $user->external_id)
            ->postJson("/api/coupons/NONEXISTENT/redeem");

        $response->assertStatus(404)
            ->assertJson(['error' => 'Coupon not found']);
    }
}
