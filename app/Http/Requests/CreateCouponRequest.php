<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'code' => 'required|string|unique:coupons,code',
            'activations_limit' => 'required|integer|min:1',
            'expires_at' => 'nullable|date|after:now',
        ];
    }

    public function authorize(): bool
    {
        return true;
    }
}
