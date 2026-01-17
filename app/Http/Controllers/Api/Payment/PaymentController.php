<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Http\Request;
use App\Traits\ApiResponseTrait;
use App\Http\Controllers\Controller;
use App\Services\PaymentService;
use App\Http\Resources\WebsiteUser\OrderResource;

class PaymentController extends Controller
{
    use ApiResponseTrait;
    protected $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * بدء عملية الدفع للطلب
     */
    public function initiatePayment(Request $request, $orderId)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('id', $orderId)
            ->firstOrFail();

        // التحقق من حالة الطلب
        if ($order->order_status_id != 1) {
            return $this->errorResponse('لا يمكن الدفع لهذا الطلب', 400);
        }

        // التحقق من أن المستخدم قد اختار عرضاً
        $acceptedOffer = $order->offers()->where('status', 'pending')->first();
        if (!$acceptedOffer) {
            return $this->errorResponse('يجب اختيار عرض أولاً', 400);
        }

        $validated = $request->validate([
            'payment_method' => 'required|in:wallet,credit_card,mada,apple_pay',
            'save_card' => 'nullable|boolean',
        ]);

        try {
            $paymentResult = $this->paymentService->processOrderPayment(
                auth()->user(),
                $order,
                $acceptedOffer,
                $validated['payment_method'],
                $validated['save_card'] ?? false
            );

            if ($paymentResult['success']) {
                // تحديث حالة الدفع في الطلب
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_PAID,
                    'payment_method' => $validated['payment_method'],
                    'payment_transaction_id' => $paymentResult['transaction_id'],
                ]);

                return $this->successResponse([
                    'order' => new OrderResource($order),
                    'payment' => $paymentResult,
                ], 'تمت عملية الدفع بنجاح');
            }

            return $this->errorResponse($paymentResult['message'], 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * التحقق من حالة الدفع
     */
    public function checkPaymentStatus($orderId)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('id', $orderId)
            ->firstOrFail();

        $response = [
            'order_id' => $order->id,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'can_confirm_driver' => $order->isPaid(),
            'price' => $order->price,
        ];

        if ($order->payment_status === 'pending') {
            $response['payment_details'] = $this->paymentService->getPaymentDetails($order);
        }

        return $this->successResponse($response);
    }

    /**
     * استرداد المبلغ (في حالة إلغاء الطلب)
     */
    public function refundPayment(Request $request, $orderId)
    {
        $order = Order::where('user_id', auth()->id())
            ->where('id', $orderId)
            ->firstOrFail();

        if (!$order->isPaid()) {
            return $this->errorResponse('لم يتم دفع هذا الطلب', 400);
        }

        if (!in_array($order->order_status_id, [1, 2])) { // pending or accepted
            return $this->errorResponse('لا يمكن استرداد المبلغ لهذا الطلب', 400);
        }

        $validated = $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            $refundResult = $this->paymentService->refundOrderPayment(
                $order,
                $validated['reason']
            );

            if ($refundResult['success']) {
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_REFUNDED,
                ]);

                // إنشاء سجل للاسترداد
                LedgerEntry::create([
                    'wallet_id' => auth()->user()->wallet->id,
                    'wallet_type' => 'user',
                    'owner_type' => LedgerEntry::OWNER_TYPE_USER,
                    'owner_id' => auth()->id(),
                    'type' => LedgerEntry::TYPE_REFUND,
                    'amount' => $order->price,
                    'status' => LedgerEntry::STATUS_COMPLETED,
                    'description' => 'استرداد مبلغ الطلب رقم ' . $order->id,
                    'metadata' => [
                        'order_id' => $order->id,
                        'refund_reason' => $validated['reason'],
                        'refund_transaction_id' => $refundResult['transaction_id'],
                    ],
                ]);

                return $this->successResponse([
                    'refund' => $refundResult,
                ], 'تم استرداد المبلغ بنجاح');
            }

            return $this->errorResponse($refundResult['message'], 400);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * الحصول على طرق الدفع المتاحة
     */
    public function getPaymentMethods()
    {
        $methods = [
            [
                'id' => 'wallet',
                'name' => 'المحفظة الإلكترونية',
                'description' => 'الدفع من رصيدك في المحفظة',
                'icon' => 'wallet',
                'available' => auth()->user()->wallet->balance >= 0,
            ],
            [
                'id' => 'credit_card',
                'name' => 'بطاقة ائتمان',
                'description' => 'Visa, MasterCard, Mada',
                'icon' => 'credit-card',
                'available' => true,
            ],
            [
                'id' => 'mada',
                'name' => 'مدى',
                'description' => 'بطاقات مدى',
                'icon' => 'credit-card',
                'available' => true,
            ],
            [
                'id' => 'apple_pay',
                'name' => 'Apple Pay',
                'description' => 'الدفع عبر Apple Pay',
                'icon' => 'apple',
                'available' => request()->header('User-Agent', '')->contains('iPhone'),
            ],
        ];

        return $this->successResponse($methods);
    }
}
