<?php

namespace App\Services\Payment;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderOffer;
use App\Services\Wallet\UserWalletService;
use App\Services\Payment\Factories\PaymentGatewayFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Notifications\PaymentSuccessful;
use App\Notifications\OrderPaid;

class PaymentService
{
    private PaymentGatewayFactory $gatewayFactory;
    private UserWalletService $walletService;

    public function __construct(
        PaymentGatewayFactory $gatewayFactory,
        UserWalletService $walletService
    ) {
        $this->gatewayFactory = $gatewayFactory;
        $this->walletService = $walletService;
    }

    public function processOrderPayment(
        User $user,
        Order $order,
        OrderOffer $offer,
        string $gateway,
        string $paymentMethod,
        array $additionalData = []
    ): array {
        DB::beginTransaction();

        try {
            // التحقق من صحة الطلب والعرض
            $this->validatePaymentRequest($order, $offer);

            $amount = $offer->price;
            $orderData = $this->prepareOrderData($order, $offer, $user, $gateway, $additionalData);


            // اختيار Gateway المناسب
            if ($gateway === 'wallet') {
                $result = $this->processWalletPayment($user, $order, $amount);
            } else {

                $paymentGateway = $this->gatewayFactory->make($gateway);

                $result = $paymentGateway->initiatePayment($orderData);
            }

            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'Payment failed');
            }
            // حفظ بيانات الدفع
            $this->savePaymentData($order, $offer, $gateway, $paymentMethod, $result);

            DB::commit();

            return [
                'success' => true,
                'message' => 'Payment initiated successfully',
                'payment' => $result,
                'order' => $order->fresh(),
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::channel('payment')->error('Payment Processing Failed', [
                'user_id' => $user->id,
                'order_id' => $order->id,
                'gateway' => $gateway,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'error_code' => 'PAYMENT_PROCESSING_FAILED',
            ];
        }
    }

    private function validatePaymentRequest(Order $order, OrderOffer $offer): void
    {
        if ($order->isPaid()) {
            throw new \Exception(message: 'Order is already paid');
        }

        if ($offer->status !== 'pending') {
            throw new \Exception('Offer is not available for payment');
        }

        // if ($order->driver_id !== $offer->driver_id) {
        //     throw new \Exception('Offer does not belong to the selected driver');
        // }
    }

    private function prepareOrderData(
        Order $order,
        OrderOffer $offer,
        User $user,
        string $gateway,
        array $additionalData
    ): array {
        $baseData = [
            'order_id' => $order->id,
            'amount' => $offer->price,
            'description' => "Order #{$order->order_number} - Water Delivery",
            'customer' => [
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'items' => $this->getOrderItems($order, $offer),
            'billing_address' => $this->getBillingAddress($order),
            'shipping_address' => $this->getShippingAddress($order),
            'callback_urls' => [
                'success' => route('payment.callback.success', ['gateway' => $gateway]),
                'failure' => route('payment.callback.failure', ['gateway' => $gateway]),
                'cancel' => route('payment.callback.cancel', ['gateway' => $gateway]),
            ],
            'metadata' => [
                'order_number' => $order->order_number,
                'offer_id' => $offer->id,
                'driver_id' => $offer->driver_id,
                'service_type' => $order->service->name ?? 'Water Delivery',
            ],
        ];

        return array_merge($baseData, $additionalData);
    }

    private function getOrderItems(Order $order, OrderOffer $offer): array
    {
        return [
            [
                'name' => $order->service->name ?? 'Water Delivery Service',
                'description' => 'Water delivery to your location',
                'quantity' => 1,
                'unit_price' => $offer->price,
                'total_price' => $offer->price,
                'sku' => 'SERVICE-' . $order->service_id,
            ]
        ];
    }

    private function getBillingAddress(Order $order): array
    {
        $location = $order->location;

        return [
            'first_name' => $order->user->first_name,
            'last_name' => $order->user->last_name,
            'address_line1' => $location->address ?? 'Not specified',
            'city' => $location->city ?? 'Riyadh',
            'state' => $location->region ?? 'Riyadh',
            'country' => 'SA',
            'postal_code' => $location->postal_code ?? '',
            'phone' => $order->user->phone,
        ];
    }

    private function getShippingAddress(Order $order): array
    {
        return $this->getBillingAddress($order);
    }

    private function processWalletPayment(User $user, Order $order, float $amount): array
    {
        $walletEntry = $this->walletService->withdraw($user, $amount, [
            'description' => 'Payment for Order #' . $order->order_number,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
            ]
        ]);

        return [
            'success' => true,
            'transaction_id' => 'WALLET-' . $walletEntry->id,
            'amount' => $amount,
            'gateway' => 'wallet',
            'message' => 'Payment processed from wallet',
        ];
    }

    private function savePaymentData(
        Order $order,
        OrderOffer $offer,
        string $gateway,
        string $paymentMethod,
        array $paymentResult
    ): void {
        $order->update([
            'payment_status' => Order::PAYMENT_STATUS_PENDING,
            'payment_method' => $paymentMethod,
            'payment_gateway' => $gateway,
            'payment_transaction_id' => $paymentResult['payment_id'] ?? null,
            'payment_details' => array_merge(
                $order->payment_details ?? [],
                [
                    'gateway' => $gateway,
                    'method' => $paymentMethod,
                    'initiated_at' => now(),
                    'payment_data' => $paymentResult,
                ]
            ),
        ]);

        // تحديث حالة العرض إلى "قيد الدفع"
        $offer->update(['status' => 'payment_pending']);
    }

    public function verifyPayment(Order $order): array
    {
        try {
            if (!$order->payment_gateway || !$order->payment_transaction_id) {
                throw new \Exception('No payment information found');
            }

            if ($order->payment_gateway === 'wallet') {
                return [
                    'success' => true,
                    'status' => 'paid',
                    'gateway' => 'wallet',
                    'verified' => true,
                ];
            }

            $paymentGateway = $this->gatewayFactory->make($order->payment_gateway);

            $verificationData = [
                'payment_id' => $order->payment_transaction_id,
                'order_id' => $order->id,
            ];

            $result = $paymentGateway->verifyPayment($verificationData);

            if ($result['success'] && in_array($result['status'], ['captured', 'approved', 'success'])) {
                $this->completePayment($order, $result);
            }

            return $result;
        } catch (\Exception $e) {
            Log::channel('payment')->error('Payment Verification Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'PAYMENT_VERIFICATION_FAILED',
            ];
        }
    }

    private function completePayment(Order $order, array $paymentResult): void
    {
        DB::beginTransaction();

        try {
            // تحديث حالة الطلب
            $order->update([
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'paid_at' => now(),
                'payment_details' => array_merge(
                    $order->payment_details ?? [],
                    [
                        'verified_at' => now(),
                        'verification_data' => $paymentResult,
                        'status' => 'completed',
                    ]
                ),
            ]);

            // تحديث حالة العرض إلى "مدفوع"
            $offer = $order->offers()->where('driver_id', $order->driver_id)->first();
            if ($offer) {
                $offer->update(['status' => 'paid']);
            }

            // إرسال الإشعارات
            $this->sendPaymentNotifications($order);

            DB::commit();

            Log::channel('payment')->info('Payment Completed Successfully', [
                'order_id' => $order->id,
                'gateway' => $order->payment_gateway,
                'amount' => $order->getPaymentAmount(),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function handleWebhook(string $gateway, array $data): array
    {
        try {
            $paymentGateway = $this->gatewayFactory->make($gateway);

            if (!$paymentGateway->isWebhookValid($data)) {
                throw new \Exception('Invalid webhook signature');
            }

            $result = $paymentGateway->handleWebhook($data);

            if ($result['success'] && $result['handled']) {
                $this->processWebhookEvent($gateway, $result);
            }

            return $result;
        } catch (\Exception $e) {
            Log::channel('payment')->error('Webhook Processing Failed', [
                'gateway' => $gateway,
                'data' => $data,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'WEBHOOK_PROCESSING_FAILED',
            ];
        }
    }

    private function processWebhookEvent(string $gateway, array $webhookData): void
    {
        $orderId = $webhookData['order_id'] ?? null;
        $status = $webhookData['status'] ?? null;

        if (!$orderId) {
            return;
        }

        $order = Order::find($orderId);
        if (!$order) {
            Log::channel('payment')->warning('Order not found for webhook', [
                'order_id' => $orderId,
                'gateway' => $gateway,
            ]);
            return;
        }

        switch ($status) {
            case 'approved':
            case 'captured':
            case 'success':
                $this->completePayment($order, $webhookData);
                break;

            case 'declined':
            case 'failed':
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_FAILED,
                    'payment_details' => array_merge(
                        $order->payment_details ?? [],
                        [
                            'failed_at' => now(),
                            'failure_reason' => $webhookData['error'] ?? 'Payment declined',
                        ]
                    ),
                ]);
                break;

            case 'refunded':
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_REFUNDED,
                    'payment_details' => array_merge(
                        $order->payment_details ?? [],
                        [
                            'refunded_at' => now(),
                            'refund_data' => $webhookData,
                        ]
                    ),
                ]);
                break;
        }
    }

    public function refundPayment(Order $order, string $reason = ''): array
    {
        try {
            if (!$order->isPaid()) {
                throw new \Exception('Order is not paid');
            }

            if ($order->payment_gateway === 'wallet') {
                $result = $this->refundWalletPayment($order, $reason);
            } else {
                $paymentGateway = $this->gatewayFactory->make($order->payment_gateway);
                $result = $paymentGateway->refundPayment(
                    $order->payment_transaction_id,
                    $order->getPaymentAmount(),
                    $reason
                );
            }

            if ($result['success']) {
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_REFUNDED,
                    'payment_details' => array_merge(
                        $order->payment_details ?? [],
                        [
                            'refunded_at' => now(),
                            'refund_reason' => $reason,
                            'refund_data' => $result,
                        ]
                    ),
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            Log::channel('payment')->error('Refund Processing Failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => $e->getMessage(),
                'error_code' => 'REFUND_PROCESSING_FAILED',
            ];
        }
    }

    private function refundWalletPayment(Order $order, string $reason)
    {
        $walletEntry = $this->walletService->deposit($order->user, $order->getPaymentAmount(), [
            'description' => 'Refund for Order #' . $order->order_number,
            'metadata' => [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'refund_reason' => $reason,
            ]
        ]);

        return [
            'success' => true,
            'transaction_id' => 'REFUND-WALLET-' . $walletEntry->id,
            'amount' => $order->getPaymentAmount(),
            'gateway' => 'wallet',
            'message' => 'Refund processed to wallet',
        ];
    }

    public function getAvailableGateways(): array
    {
        return $this->gatewayFactory->getAvailableGateways();
    }

    private function sendPaymentNotifications(Order $order): void
    {
        try {
            // إشعار للمستخدم
            if ($order->user) {
                $order->user->notify(new PaymentSuccessful($order));
            }

            // إشعار للسائق
            if ($order->driver) {
                $order->driver->notify(new OrderPaid($order));
            }
        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to send payment notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
