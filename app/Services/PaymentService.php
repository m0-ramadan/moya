<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderOffer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\Payment\PaymobService;
use App\Services\Wallet\UserWalletService;
// use App\Services\PaymentGateways\PaymobService;
// use App\Services\PaymentGateways\WalletService;

class PaymentService
{
    protected $paymobService;
    protected $walletService;

    public function __construct(
        PaymobService $paymobService,
        UserWalletService $walletService
    ) {
        $this->paymobService = $paymobService;
        $this->walletService = $walletService;
    }

  /**
     * معالجة دفع الطلب
     */
    public function processOrderPayment(
        User $user,
        Order $order,
        OrderOffer $offer,
        string $paymentMethod,
        bool $saveCard = false
    ): array {
        DB::beginTransaction();

        try {
            $amount = $offer->price;

            switch ($paymentMethod) {
                case Order::PAYMENT_METHOD_WALLET:
                    $result = $this->processWalletPayment($user, $order, $amount);
                    break;

                case Order::PAYMENT_METHOD_CREDIT_CARD:
                case Order::PAYMENT_METHOD_MADA:
                case Order::PAYMENT_METHOD_APPLE_PAY:
                case Order::PAYMENT_METHOD_PAYMOB:
                    $result = $this->processCardPayment($user, $order, $amount, $paymentMethod);
                    break;

                default:
                    throw new \Exception('طريقة الدفع غير مدعومة');
            }

            if ($result['success']) {
                // تحديث حالة العرض
                $offer->update(['status' => 'accepted']);

                // تحديث حالة الطلب
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_PAID,
                    'payment_method' => $paymentMethod,
                    'payment_transaction_id' => $result['transaction_id'] ?? null,
                    'paid_at' => now(),
                ]);

                DB::commit();
                return $result;
            }

            DB::rollBack();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment processing failed', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'PAYMENT_PROCESSING_FAILED'
            ];
        }
    }

    /**
     * الدفع عبر المحفظة
     */
    private function processWalletPayment(User $user, Order $order, float $amount): array
    {
        try {
            // خصم المبلغ من المحفظة
            $walletEntry = $this->walletService->withdraw($user, $amount, [
                'description' => 'دفع للطلب رقم #' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                ]
            ]);

            return [
                'success' => true,
                'transaction_id' => 'WALLET-' . $walletEntry->id,
                'amount' => $amount,
                'payment_method' => 'wallet',
                'message' => 'تم الدفع بنجاح من المحفظة'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'WALLET_PAYMENT_FAILED'
            ];
        }
    }

    /**
     * الدفع بالبطاقة
     */
    private function processCardPayment(User $user, Order $order, float $amount, string $paymentMethod): array
    {
        try {
            // إنشاء طلب دفع عبر Paymob
            $paymentResult = $this->paymobService->createPaymentOrder([
                'user' => $user,
                'amount' => $amount,
                'wallet_currency' => 'SAR',
                'order_id' => 'ORDER-' . $order->order_number,
                'callback_url' => route('paymob.webhook'),
            ]);

            if (!$paymentResult['success']) {
                throw new \Exception($paymentResult['error'] ?? 'فشل إنشاء طلب الدفع');
            }

            return [
                'success' => true,
                'payment_url' => $paymentResult['payment_url'],
                'transaction_id' => $paymentResult['order_id'],
                'amount' => $amount,
                'payment_method' => $paymentMethod,
                'message' => 'تم إنشاء طلب الدفع بنجاح'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'CARD_PAYMENT_FAILED'
            ];
        }
    }

    /**
     * الحصول على تفاصيل الدفع
     */
    public function getPaymentDetails(Order $order): array
    {
        if ($order->payment_transaction_id && strpos($order->payment_transaction_id, 'PAYMOB-') === 0) {
            $transactionId = str_replace('PAYMOB-', '', $order->payment_transaction_id);
            $status = $this->paymobService->getTransactionStatus($transactionId);
            
            return [
                'provider' => 'paymob',
                'status' => $status['status'] ?? 'unknown',
                'details' => $status['data'] ?? [],
            ];
        }

        return [
            'provider' => 'wallet',
            'status' => $order->payment_status,
            'details' => [],
        ];
    }

    /**
     * استرداد مبلغ الطلب
     */
    public function refundOrderPayment(Order $order, string $reason): array
    {
        DB::beginTransaction();

        try {
            $amount = $order->getPaymentAmount();

            switch ($order->payment_method) {
                case Order::PAYMENT_METHOD_WALLET:
                    $result = $this->refundWalletPayment($order, $amount, $reason);
                    break;

                case Order::PAYMENT_METHOD_CREDIT_CARD:
                case Order::PAYMENT_METHOD_MADA:
                case Order::PAYMENT_METHOD_APPLE_PAY:
                case Order::PAYMENT_METHOD_PAYMOB:
                    $result = $this->refundCardPayment($order, $amount, $reason);
                    break;

                default:
                    throw new \Exception('طريقة الدفع غير مدعومة للاسترداد');
            }

            if ($result['success']) {
                DB::commit();
                return $result;
            }

            DB::rollBack();
            return $result;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Refund processing failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'REFUND_PROCESSING_FAILED'
            ];
        }
    }

    /**
     * استرداد دفعة المحفظة
     */
    private function refundWalletPayment(Order $order, float $amount, string $reason): array
    {
        try {
            // إرجاع المبلغ للمستخدم
            $refundEntry = $this->walletService->deposit($order->user, $amount, [
                'description' => 'استرداد مبلغ الطلب رقم #' . $order->order_number,
                'metadata' => [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'refund_reason' => $reason,
                ]
            ]);

            return [
                'success' => true,
                'transaction_id' => 'REFUND-WALLET-' . $refundEntry->id,
                'amount' => $amount,
                'message' => 'تم استرداد المبلغ بنجاح إلى المحفظة'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'WALLET_REFUND_FAILED'
            ];
        }
    }

    /**
     * استرداد دفعة البطاقة
     */
    private function refundCardPayment(Order $order, float $amount, string $reason): array
    {
        if (!$order->payment_transaction_id || !strpos($order->payment_transaction_id, 'PAYMOB-')) {
            return [
                'success' => false,
                'message' => 'لا يوجد معرف معاملة للاسترداد',
                'error_code' => 'NO_TRANSACTION_ID'
            ];
        }

        try {
            $transactionId = str_replace('PAYMOB-', '', $order->payment_transaction_id);
            $refundResult = $this->paymobService->refund($transactionId, $amount);

            if (!$refundResult['success']) {
                throw new \Exception($refundResult['error'] ?? 'فشل استرداد المبلغ');
            }

            return [
                'success' => true,
                'transaction_id' => 'REFUND-PAYMOB-' . ($refundResult['refund_id'] ?? ''),
                'amount' => $amount,
                'message' => 'تم استرداد المبلغ بنجاح'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'CARD_REFUND_FAILED'
            ];
        }
    }

    /**
     * التحقق من حالة الدفع
     */
    public function checkPaymentStatus(Order $order): array
    {
        if ($order->isPaid()) {
            return [
                'status' => 'paid',
                'paid_at' => $order->paid_at,
                'amount' => $order->getPaymentAmount(),
            ];
        }

        if ($order->payment_transaction_id && strpos($order->payment_transaction_id, 'PAYMOB-') === 0) {
            $transactionId = str_replace('PAYMOB-', '', $order->payment_transaction_id);
            $status = $this->paymobService->getTransactionStatus($transactionId);
            
            return [
                'status' => $status['status'] ?? 'pending',
                'details' => $status['data'] ?? [],
            ];
        }

        return [
            'status' => $order->payment_status,
            'details' => [],
        ];
    }



}
