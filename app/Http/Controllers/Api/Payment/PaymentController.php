<?php

namespace App\Http\Controllers\Api\Payment;

use App\Models\User;
use App\Models\Order;
use Illuminate\Http\Request;
use App\Notifications\OrderPaid;
use App\Services\PaymentService;
use App\Traits\ApiResponseTrait;
use App\Models\Wallet\UserWallet;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\Payment\PaymobService;
use App\Notifications\PaymentSuccessful;
use App\Http\Resources\WebsiteUser\OrderResource;

class PaymentController extends Controller
{
    use ApiResponseTrait;
    protected $paymentService;
    protected $paymobService;

    public function __construct(PaymentService $paymentService, PaymobService $paymobService)
    {
        $this->paymentService = $paymentService;
        $this->paymobService = $paymobService;
    }

    /**
     * معالجة إشعارات الدفع من Paymob (Webhook)
     */
    public function handleWebhook(Request $request)
    {
        Log::info('Paymob Webhook Received', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'data' => $request->all()
        ]);

        try {
            $payload = $request->all();
            $type = $payload['type'] ?? null;
            $obj = $payload['obj'] ?? [];

            // التحقق من HMAC إذا كان موجوداً
            if (isset($payload['hmac'])) {
                $hmacValid = $this->paymobService->validateHmac($payload);
                if (!$hmacValid) {
                    Log::warning('Invalid HMAC signature', ['payload' => $payload]);
                    return response()->json(['success' => false, 'message' => 'Invalid HMAC'], 400);
                }
            }

            // معالجة حسب نوع الإشعار
            switch ($type) {
                case 'TRANSACTION':
                    return $this->handleTransaction($obj, $payload);

                case 'TOKEN':
                    Log::info('Paymob Token Webhook', $obj);
                    return response()->json(['success' => true]);

                case 'DISPUTE':
                    Log::warning('Paymob Dispute Webhook', $obj);
                    return $this->handleDispute($obj);

                default:
                    Log::info('Unknown Paymob Webhook Type', ['type' => $type, 'data' => $obj]);
                    return response()->json(['success' => true, 'message' => 'Unknown type, ignoring']);
            }
        } catch (\Exception $e) {
            Log::error('Paymob Webhook Error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Internal server error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * معالجة إشعارات المعاملات
     */
    private function handleTransaction(array $transaction, array $fullPayload)
    {
        $transactionId = $transaction['id'] ?? null;
        $amountCents = $transaction['amount_cents'] ?? 0;
        $amount = $amountCents / 100;
        $currency = $transaction['currency'] ?? 'SAR';
        $success = $transaction['success'] ?? false;
        $isCapture = $transaction['is_capture'] ?? false;
        $isVoided = $transaction['is_voided'] ?? false;
        $isRefunded = $transaction['is_refunded'] ?? false;

        // استخراج order ID من merchant_reference أو reference_id
        $orderId = $transaction['merchant_reference'] ??
            $transaction['reference_id'] ??
            ($transaction['order']['merchant_order_id'] ?? null);

        Log::info('Processing Paymob Transaction', [
            'transaction_id' => $transactionId,
            'order_id' => $orderId,
            'amount' => $amount,
            'success' => $success,
            'is_capture' => $isCapture,
            'type' => $this->getTransactionType($transaction)
        ]);

        // إذا كان استرداد
        if ($isRefunded) {
            return $this->handleRefund($transaction);
        }

        // إذا كان إلغاء
        if ($isVoided) {
            return $this->handleVoid($transaction);
        }

        // إذا كانت معاملة ناجحة وتم السحب
        if ($success && $isCapture) {
            return $this->handleSuccessfulPayment($transaction, $orderId, $amount, $currency);
        }

        // إذا كانت معاملة فاشلة
        if (!$success) {
            return $this->handleFailedPayment($transaction, $orderId);
        }

        // إذا كانت معاملة معلقة
        return $this->handlePendingPayment($transaction, $orderId);
    }

    /**
     * معالجة الدفع الناجح
     */
    private function handleSuccessfulPayment(array $transaction, $orderId, $amount, $currency)
    {
        DB::beginTransaction();

        try {
            // البحث عن طلب الدفعة (Order أو LedgerEntry للودائع)
            if (strpos($orderId, 'DEP-') === 0) {
                // هذا إيداع للمحفظة
                return $this->handleWalletDeposit($transaction, $orderId, $amount, $currency);
            } else {
                // هذا دفع للطلب
                return $this->handleOrderPayment($transaction, $orderId, $amount, $currency);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to process successful payment', [
                'transaction' => $transaction,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * معالجة إيداع المحفظة
     */
    private function handleWalletDeposit(array $transaction, $orderId, $amount, $currency)
    {
        // استخراج المعلومات من orderId: DEP-{amount}-{currency}-{timestamp}
        $parts = explode('-', $orderId);
        if (count($parts) < 4) {
            throw new \Exception('Invalid deposit order ID format');
        }

        $originalAmount = floatval($parts[1]);
        $originalCurrency = $parts[2];
        $timestamp = $parts[3];

        // البحث عن إدخال الدفعة المعلق
        $pendingEntry = LedgerEntry::where('reference', $orderId)
            ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
            ->where('status', LedgerEntry::STATUS_PENDING)
            ->first();

        if (!$pendingEntry) {
            Log::warning('Pending deposit entry not found', ['order_id' => $orderId]);
            return response()->json(['success' => false, 'message' => 'Pending entry not found'], 404);
        }

        // تحديث الإدخال المعلق
        $pendingEntry->update([
            'status' => LedgerEntry::STATUS_COMPLETED,
            'metadata' => array_merge($pendingEntry->metadata ?? [], [
                'payment_transaction_id' => $transaction['id'],
                'payment_data' => $transaction,
                'confirmed_at' => now(),
                'currency_charged' => $currency,
                'amount_charged' => $amount,
                'exchange_rate' => $amount / $originalAmount
            ])
        ]);

        // الحصول على المحفظة
        $wallet = UserWallet::find($pendingEntry->wallet_id);
        if (!$wallet) {
            throw new \Exception('Wallet not found');
        }

        // إنشاء إدخال الإيداع الناجح
        $depositEntry = LedgerEntry::create([
            'wallet_type' => 'user',
            'wallet_id' => $wallet->id,
            'owner_type' => LedgerEntry::OWNER_TYPE_USER,
            'owner_id' => $pendingEntry->owner_id,
            'type' => LedgerEntry::TYPE_DEPOSIT,
            'amount' => $originalAmount,
            'balance_before' => $wallet->balance,
            'balance_after' => $wallet->balance + $originalAmount,
            'description' => 'إيداع ناجح عبر Paymob',
            'status' => LedgerEntry::STATUS_COMPLETED,
            'reference' => $orderId,
            'related_entry_id' => $pendingEntry->id,
            'metadata' => [
                'payment_transaction_id' => $transaction['id'],
                'payment_method' => 'paymob',
                'transaction_data' => $transaction,
                'exchange_rate' => $amount / $originalAmount,
                'currency_charged' => $currency,
                'amount_charged' => $amount
            ]
        ]);

        // تحديث رصيد المحفظة
        $wallet->increment('balance', $originalAmount);
        $wallet->increment('available_balance', $originalAmount);
        $wallet->update([
            'last_transaction_at' => now(),
            'total_deposits_today' => DB::raw('total_deposits_today + ' . $originalAmount)
        ]);

        DB::commit();

        Log::info('Wallet deposit processed successfully', [
            'transaction_id' => $transaction['id'],
            'order_id' => $orderId,
            'wallet_id' => $wallet->id,
            'amount' => $originalAmount
        ]);

        return response()->json(['success' => true, 'message' => 'Deposit processed']);
    }

    /**
     * معالجة دفع الطلب
     */
    private function handleOrderPayment(array $transaction, $orderId, $amount, $currency)
    {
        $order = Order::where('order_number', $orderId)
            ->orWhere('id', $orderId)
            ->first();

        if (!$order) {
            throw new \Exception("Order {$orderId} not found");
        }

        // تحديث حالة الطلب
        $order->update([
            'payment_status' => Order::PAYMENT_STATUS_PAID,
            'payment_method' => 'paymob',
            'payment_transaction_id' => $transaction['id'],
            'paid_at' => now(),
            'payment_data' => $transaction
        ]);

        // إذا كان هناك عرض مقبول، تحديث حالته
        $acceptedOffer = $order->offers()->where('status', 'pending')->first();
        if ($acceptedOffer) {
            $acceptedOffer->update(['status' => 'paid']);
        }

        // إرسال إشعارات
        $this->sendPaymentNotifications($order, $transaction);

        DB::commit();

        Log::info('Order payment processed successfully', [
            'transaction_id' => $transaction['id'],
            'order_id' => $orderId,
            'order_number' => $order->order_number,
            'amount' => $amount
        ]);

        return response()->json(['success' => true, 'message' => 'Order payment processed']);
    }

    /**
     * معالجة الدفع الفاشل
     */
    private function handleFailedPayment(array $transaction, $orderId)
    {
        $errorMsg = $transaction['error_occured'] ? 'Payment failed' : 'Unknown error';
        $dataMessage = $transaction['data']['message'] ?? null;

        Log::warning('Payment failed', [
            'transaction' => $transaction,
            'order_id' => $orderId,
            'error' => $dataMessage
        ]);

        // تحديث إدخال الدفعة المعلق إذا كان إيداع
        if (strpos($orderId, 'DEP-') === 0) {
            $pendingEntry = LedgerEntry::where('reference', $orderId)
                ->where('type', LedgerEntry::TYPE_DEPOSIT_PENDING)
                ->where('status', LedgerEntry::STATUS_PENDING)
                ->first();

            if ($pendingEntry) {
                $pendingEntry->update([
                    'status' => LedgerEntry::STATUS_FAILED,
                    'metadata' => array_merge($pendingEntry->metadata ?? [], [
                        'payment_transaction_id' => $transaction['id'],
                        'failed_at' => now(),
                        'failure_reason' => $dataMessage,
                        'transaction_data' => $transaction
                    ])
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Failed payment recorded']);
    }

    /**
     * معالجة الدفع المعلق
     */
    private function handlePendingPayment(array $transaction, $orderId)
    {
        Log::info('Payment pending', [
            'transaction' => $transaction,
            'order_id' => $orderId
        ]);

        return response()->json(['success' => true, 'message' => 'Pending payment recorded']);
    }

    /**
     * معالجة الاسترداد
     */
    private function handleRefund(array $transaction)
    {
        $originalTransactionId = $transaction['parent_transaction'] ?? null;
        $amountCents = $transaction['amount_cents'] ?? 0;
        $amount = $amountCents / 100;

        Log::info('Refund processed', [
            'refund_id' => $transaction['id'],
            'original_transaction' => $originalTransactionId,
            'amount' => $amount
        ]);

        // البحث عن المعاملة الأصلية
        $originalEntry = LedgerEntry::where('metadata->payment_transaction_id', $originalTransactionId)
            ->first();

        if ($originalEntry) {
            $originalEntry->update([
                'metadata' => array_merge($originalEntry->metadata ?? [], [
                    'refunded_at' => now(),
                    'refund_amount' => $amount,
                    'refund_transaction_id' => $transaction['id']
                ])
            ]);
        }

        return response()->json(['success' => true, 'message' => 'Refund processed']);
    }

    /**
     * معالجة الإلغاء
     */
    private function handleVoid(array $transaction)
    {
        Log::info('Transaction voided', ['transaction' => $transaction]);
        return response()->json(['success' => true, 'message' => 'Void processed']);
    }

    /**
     * معالجة النزاع
     */
    private function handleDispute(array $dispute)
    {
        Log::warning('Payment dispute received', ['dispute' => $dispute]);

        // هنا يمكنك إضافة منطق إدارة النزاعات
        // مثل تحديث حالة الطلب، إرسال إشعارات، إلخ

        return response()->json(['success' => true, 'message' => 'Dispute recorded']);
    }

    /**
     * إرسال إشعارات بعد الدفع الناجح
     */
    private function sendPaymentNotifications(Order $order, array $transaction)
    {
        try {
            // إشعار للمستخدم
            if ($order->user) {
                // إرسال إشعار في التطبيق
                $order->user->notify(new \App\Notifications\PaymentSuccessful($order));

                // إرسال رسالة WhatsApp (اختياري)
                if ($order->user->phone) {
                    // $this->sendWhatsAppMessage($order->user->phone, ...);
                }
            }

            // إشعار للسائق إذا كان هناك عرض مقبول
            if ($acceptedOffer = $order->offers()->where('status', 'paid')->first()) {
                if ($acceptedOffer->driver) {
                    $acceptedOffer->driver->notify(new OrderPaid($order));
                }
            }

            // إشعار للإدارة
            // $adminUsers = User::where('role', 'admin')->get();
            // foreach ($adminUsers as $admin) {
            //     $admin->notify(new \App\Notifications\NewPayment($order, $transaction));
            // }

        } catch (\Exception $e) {
            Log::error('Failed to send payment notifications', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * تحديد نوع المعاملة
     */
    private function getTransactionType(array $transaction): string
    {
        if ($transaction['is_refunded'] ?? false) return 'refund';
        if ($transaction['is_voided'] ?? false) return 'void';
        if ($transaction['is_capture'] ?? false) return 'capture';
        if ($transaction['success'] ?? false) return 'success';
        if ($transaction['pending'] ?? false) return 'pending';
        return 'unknown';
    }

    /**
     * تحقق من صحة HMAC يدوياً (للاحتياط)
     */
    private function validateHmacManually(array $payload): bool
    {
        if (!isset($payload['hmac'])) {
            return false;
        }

        $hmac = $payload['hmac'];
        unset($payload['hmac']);

        ksort($payload);

        $concatenated = '';
        foreach ($payload as $key => $value) {
            if (is_array($value)) {
                $concatenated .= json_encode($value, JSON_UNESCAPED_SLASHES);
            } else {
                $concatenated .= $value;
            }
        }

        $secret = config('services.paymob.hmac_secret');
        $calculatedHmac = hash_hmac('sha512', $concatenated, $secret);

        return hash_equals($hmac, $calculatedHmac);
    }
    /**
     * بدء عملية الدفع للطلب
     */
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
            'payment_method' => 'required|in:wallet,credit_card,mada,apple_pay,paymob',
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
                // إرسال إشعارات
                auth()->user()->notify(new PaymentSuccessful($order));

                // إرسال إشعار للسائق إذا كان هناك عرض مقبول
                if ($order->driver) {
                    $order->driver->notify(new OrderPaid($order));
                }

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

        $paymentStatus = $this->paymentService->checkPaymentStatus($order);

        $response = [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_status' => $order->payment_status,
            'payment_method' => $order->payment_method,
            'can_confirm_driver' => $order->isPaid(),
            'price' => $order->getPaymentAmount(),
            'provider_status' => $paymentStatus['status'] ?? null,
        ];

        if ($order->payment_status === Order::PAYMENT_STATUS_PENDING) {
            $response['payment_details'] = $paymentStatus['details'] ?? [];
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
                // 'available' => request()->header('User-Agent', '')->contains('iPhone'),
            ],
        ];

        return $this->successResponse($methods);
    }
}
