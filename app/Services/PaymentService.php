<?php

namespace App\Services;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderOffer;
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

    public function processOrderPayment(
        User $user,
        Order $order,
        OrderOffer $offer,
        string $paymentMethod,
        bool $saveCard = false
    ): array {
        switch ($paymentMethod) {
            case 'wallet':
                return $this->processWalletPayment($user, $order, $offer);

            case 'credit_card':
            case 'mada':
            case 'apple_pay':
                return $this->processCardPayment($user, $order, $offer, $paymentMethod, $saveCard);

            default:
                throw new \Exception('طريقة الدفع غير مدعومة');
        }
    }

    private function processWalletPayment(User $user, Order $order, OrderOffer $offer): array
    {
        if ($user->wallet->balance < $offer->price) {
            return [
                'success' => false,
                'message' => 'رصيدك غير كافي في المحفظة',
                'required_amount' => $offer->price,
                'current_balance' => $user->wallet->balance,
            ];
        }

        try {
            $transaction = $this->walletService->deductFromWallet(
                $user,
                $offer->price,
                'دفع طلب رقم ' . $order->id,
                [
                    'order_id' => $order->id,
                    'offer_id' => $offer->id,
                    'driver_id' => $offer->driver_id,
                ]
            );

            return [
                'success' => true,
                'message' => 'تم الدفع بنجاح من المحفظة',
                'transaction_id' => $transaction->id,
                'amount' => $offer->price,
                'new_balance' => $user->wallet->fresh()->balance,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل عملية الدفع: ' . $e->getMessage(),
            ];
        }
    }

    private function processCardPayment(
        User $user,
        Order $order,
        OrderOffer $offer,
        string $paymentMethod,
        bool $saveCard = false
    ): array {
        try {
            $paymentData = [
                'amount' => $offer->price * 100, // تحويل للقرش
                'currency' => 'EGP',
                'order_id' => $order->id,
                'user_id' => $user->id,
                'billing_data' => [
                    'first_name' => $user->first_name,
                    'last_name' => $user->last_name,
                    'email' => $user->email,
                    'phone_number' => $user->phone,
                ],
                'integration_id' => $this->getIntegrationId($paymentMethod),
                'save_card' => $saveCard,
            ];

            $paymentResult = $this->paymobService->initiatePayment($paymentData);

            return [
                'success' => true,
                'message' => 'تم إنشاء جلسة الدفع',
                'transaction_id' => $paymentResult['transaction_id'],
                'payment_key' => $paymentResult['payment_key'],
                'iframe_url' => $paymentResult['iframe_url'],
                'redirect_url' => $paymentResult['redirect_url'],
                'amount' => $offer->price,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل في إنشاء جلسة الدفع: ' . $e->getMessage(),
            ];
        }
    }

    private function getIntegrationId(string $paymentMethod): int
    {
        $integrations = [
            'credit_card' => config('paymob.card_integration_id'),
            'mada' => config('paymob.mada_integration_id'),
            'apple_pay' => config('paymob.apple_pay_integration_id'),
        ];

        return $integrations[$paymentMethod] ?? $integrations['credit_card'];
    }

    public function refundOrderPayment(Order $order, string $reason): array
    {
        if ($order->payment_method === 'wallet') {
            return $this->refundWalletPayment($order, $reason);
        }

        return $this->refundCardPayment($order, $reason);
    }

    private function refundWalletPayment(Order $order, string $reason): array
    {
        try {
            $this->walletService->refundToWallet(
                $order->user,
                $order->price,
                'استرداد مبلغ الطلب رقم ' . $order->id,
                [
                    'order_id' => $order->id,
                    'reason' => $reason,
                ]
            );

            return [
                'success' => true,
                'message' => 'تم استرداد المبلغ إلى المحفظة',
                'amount' => $order->price,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل في استرداد المبلغ: ' . $e->getMessage(),
            ];
        }
    }

    private function refundCardPayment(Order $order, string $reason): array
    {
        try {
            $refundResult = $this->paymobService->refundTransaction(
                $order->payment_transaction_id,
                $order->price * 100,
                $reason
            );

            return [
                'success' => true,
                'message' => 'تم طلب استرداد المبلغ',
                'transaction_id' => $refundResult['refund_id'],
                'amount' => $order->price,
                'status' => $refundResult['status'],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'فشل في استرداد المبلغ: ' . $e->getMessage(),
            ];
        }
    }
}
