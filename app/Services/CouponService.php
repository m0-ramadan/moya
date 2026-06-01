<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\CouponUsage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CouponService
{
    public function applyCouponToOrder(Order $order, User $user, float $baseAmount, string $couponCode): array
    {
        $coupon = Coupon::query()->code($couponCode)->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => 'الكوبون غير موجود.',
            ]);
        }

        $summary = $this->buildCouponSummary($coupon, $order, $user, $baseAmount);

        $this->persistOrderCoupon($order, $coupon, $summary);

        return $summary;
    }

    public function revalidateAppliedCoupon(Order $order, User $user, float $baseAmount): array
    {
        if (! $order->coupon_id) {
            return $this->clearCouponFromOrder($order, $baseAmount);
        }

        $coupon = $order->coupon;

        if (! $coupon) {
            return $this->clearCouponFromOrder($order, $baseAmount);
        }

        $summary = $this->buildCouponSummary($coupon, $order, $user, $baseAmount);

        $this->persistOrderCoupon($order, $coupon, $summary);

        return $summary;
    }

    public function clearCouponFromOrder(Order $order, float $baseAmount): array
    {
        $summary = [
            'coupon' => null,
            'original_amount' => round($baseAmount, 2),
            'discount_amount' => 0.0,
            'final_amount' => round($baseAmount, 2),
        ];

        $order->update([
            'coupon_id' => null,
            'coupon_code' => null,
            'coupon_type' => null,
            'coupon_value' => null,
            'original_amount' => $summary['original_amount'],
            'discount_amount' => $summary['discount_amount'],
            'final_amount' => $summary['final_amount'],
        ]);

        return $summary;
    }

    public function recordUsage(Order $order, ?Request $request = null): ?CouponUsage
    {
        if (! $order->coupon_id) {
            return null;
        }

        return CouponUsage::firstOrCreate(
            ['order_id' => $order->id],
            [
                'coupon_id' => $order->coupon_id,
                'user_id' => $order->user_id,
                'discount_amount' => $order->discount_amount ?? 0,
                'original_amount' => $order->original_amount ?? $order->getPaymentAmount(),
                'final_amount' => $order->final_amount ?? $order->getPaymentAmount(),
                'ip_address' => $request?->ip(),
                'session_id' => $request?->session()?->getId(),
                'metadata' => [
                    'coupon_code' => $order->coupon_code,
                    'payment_gateway' => $order->payment_gateway,
                    'payment_method' => $order->payment_method,
                ],
            ]
        );
    }

    private function buildCouponSummary(Coupon $coupon, Order $order, User $user, float $baseAmount): array
    {
        if (! $coupon->is_active) {
            throw ValidationException::withMessages([
                'coupon_code' => 'هذا الكوبون غير مفعل حالياً.',
            ]);
        }

        if (! $coupon->isStarted()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'هذا الكوبون لم يبدأ بعد.',
            ]);
        }

        if ($coupon->isExpired()) {
            throw ValidationException::withMessages([
                'coupon_code' => 'انتهت صلاحية هذا الكوبون.',
            ]);
        }

        if ($coupon->min_order_amount !== null && $baseAmount < (float) $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => 'قيمة الطلب أقل من الحد الأدنى المطلوب لاستخدام هذا الكوبون.',
            ]);
        }

        $totalUsages = $coupon->usages()->count();

        if ($coupon->max_uses !== null && $totalUsages >= $coupon->max_uses) {
            throw ValidationException::withMessages([
                'coupon_code' => 'تم استهلاك الحد الأقصى لاستخدام هذا الكوبون.',
            ]);
        }

        $userUsages = $coupon->usages()->where('user_id', $user->id)->count();

        if ($coupon->max_uses_per_user !== null && $userUsages >= $coupon->max_uses_per_user) {
            throw ValidationException::withMessages([
                'coupon_code' => 'لقد استخدمت هذا الكوبون بالحد الأقصى المسموح.',
            ]);
        }

        $discountAmount = $this->calculateDiscountAmount($coupon, $baseAmount);
        $finalAmount = max(round($baseAmount - $discountAmount, 2), 0);

        return [
            'coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
                'name' => $coupon->name,
                'type' => $coupon->type,
                'value' => (float) $coupon->value,
            ],
            'original_amount' => round($baseAmount, 2),
            'discount_amount' => $discountAmount,
            'final_amount' => $finalAmount,
        ];
    }

    private function calculateDiscountAmount(Coupon $coupon, float $baseAmount): float
    {
        $discountAmount = $coupon->type === 'percentage'
            ? ($baseAmount * ((float) $coupon->value / 100))
            : (float) $coupon->value;

        return round(min($discountAmount, $baseAmount), 2);
    }

    private function persistOrderCoupon(Order $order, Coupon $coupon, array $summary): void
    {
        $order->update([
            'coupon_id' => $coupon->id,
            'coupon_code' => $coupon->code,
            'coupon_type' => $coupon->type,
            'coupon_value' => $coupon->value,
            'original_amount' => $summary['original_amount'],
            'discount_amount' => $summary['discount_amount'],
            'final_amount' => $summary['final_amount'],
        ]);
    }
}
