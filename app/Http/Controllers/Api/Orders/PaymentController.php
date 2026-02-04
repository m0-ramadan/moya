<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Payment\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Resources\WebsiteUser\OrderResource;
use App\Traits\ApiResponseTrait;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    private PaymentService $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    /**
     * بدء عملية الدفع
     */
    public function initiatePayment(Request $request, Order $order)
    {
        $request->validate([
            'offer_id' => 'required|exists:order_offers,id',
            'gateway' => 'required|in:wallet,paymob,tamara,tabby',
            'payment_method' => 'required|string',
            'save_card' => 'nullable|boolean',
        ]);

        try {
            $user = $request->user();
            $offer = $order->offers()->findOrFail($request->input('offer_id'));

            $additionalData = [
                'save_card' => $request->input('save_card', false),
                'installments' => $request->input('installments'),
                'metadata' => $request->only(['device_id', 'ip_address']),
            ];

            $result = $this->paymentService->processOrderPayment(
                $user,
                $order,
                $offer,
                $request->input('gateway'),
                $request->input('payment_method'),
                $additionalData
            );

            if (!$result['success']) {
                return $this->errorResponse($result['message'], 400, $result['error_code'] ?? null);
            }

            return $this->successResponse([
                'order' => new OrderResource($order->fresh()),
                'payment' => $result['payment'],
            ], 'Payment initiated successfully');
        } catch (\Exception $e) {
            Log::channel('payment')->error('Payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * التحقق من حالة الدفع
     */
    public function checkPaymentStatus(Order $order)
    {
        try {
            $result = $this->paymentService->verifyPayment($order);

            $response = [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'payment_status' => $order->payment_status,
                'payment_method' => $order->payment_method,
                'payment_gateway' => $order->payment_gateway,
                'can_confirm_driver' => $order->isPaid(),
                'price' => $order->getPaymentAmount(),
                'verification_result' => $result,
            ];

            return $this->successResponse($response);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * معالجة Webhook للبوابات المختلفة
     */
    public function handleWebhook(Request $request, string $gateway)
    {
        Log::channel('payment')->info("{$gateway} Webhook Received", [
            'gateway' => $gateway,
            'ip' => $request->ip(),
            'data' => $request->all(),
        ]);

        try {
            $data = $request->all();

            $result = $this->paymentService->handleWebhook($gateway, $data);

            if ($result['success']) {
                return response()->json(['success' => true], 200);
            }

            return response()->json([
                'success' => false,
                'error' => $result['error'],
                'error_code' => $result['error_code'] ?? null,
            ], 400);
        } catch (\Exception $e) {
            Log::channel('payment')->error("{$gateway} Webhook Error", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'error' => 'Internal server error',
            ], 500);
        }
    }

    /**
     * استرداد المبلغ
     */
    public function refundPayment(Request $request, Order $order)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        try {
            if (!$order->isPaid()) {
                return $this->errorResponse('Order is not paid', 400);
            }

            $result = $this->paymentService->refundPayment(
                $order,
                $request->input('reason')
            );

            if (!$result['success']) {
                return $this->errorResponse($result['error'], 400, $result['error_code'] ?? null);
            }

            return $this->successResponse([
                'refund' => $result,
            ], 'Refund processed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * الحصول على طرق الدفع المتاحة
     */
    public function getPaymentMethods()
    {
        try {
            $gateways = $this->paymentService->getAvailableGateways();

            $availableMethods = [];

            foreach ($gateways as $gatewayKey => $gatewayInfo) {
                $availableMethods[] = [
                    'id' => $gatewayKey,
                    'name' => $gatewayInfo['name'],
                    'description' => $gatewayInfo['description'],
                    'methods' => $gatewayInfo['methods'],
                    'icon' => $gatewayInfo['icon'],
                    'supports_installments' => $gatewayInfo['supports_installments'] ?? false,
                    'supports_save_card' => $gatewayInfo['supports_save_card'] ?? false,
                    'requires_balance' => $gatewayInfo['requires_balance'] ?? false,
                ];
            }

            return $this->successResponse($availableMethods);
        } catch (\Exception $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * صفحة نجاح الدفع
     */
    public function paymentSuccess(Request $request, string $gateway)
    {
        $orderId = $request->input('order_id') ?? $request->input('order_reference_id');

        if (!$orderId) {
            return redirect()->route('orders.index')
                ->with('error', 'بيانات الدفع غير صحيحة');
        }

        $order = Order::where('id', $orderId)
            ->orWhere('order_number', $orderId)
            ->first();

        if (!$order) {
            return redirect()->route('orders.index')
                ->with('error', 'الطلب غير موجود');
        }

        try {
            // التحقق من الدفع
            $this->paymentService->verifyPayment($order);

            return redirect()->route('orders.show', $order)
                ->with('success', 'تم الدفع بنجاح وتأكيد الطلب مع السائق');
        } catch (\Exception $e) {
            return redirect()->route('orders.show', $order)
                ->with('warning', 'جاري معالجة الدفع، سيتم تحديث الحالة قريباً');
        }
    }

    /**
     * صفحة فشل الدفع
     */
    public function paymentFailure(Request $request, string $gateway)
    {
        $orderId = $request->input('order_id') ?? $request->input('order_reference_id');

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                return redirect()->route('orders.show', $order)
                    ->with('error', 'فشل عملية الدفع، يرجى المحاولة مرة أخرى');
            }
        }

        return redirect()->route('orders.index')
            ->with('error', 'فشل عملية الدفع');
    }

    /**
     * صفحة إلغاء الدفع
     */
    public function paymentCancel(Request $request, string $gateway)
    {
        $orderId = $request->input('order_id') ?? $request->input('order_reference_id');

        if ($orderId) {
            $order = Order::find($orderId);
            if ($order) {
                return redirect()->route('orders.show', $order)
                    ->with('warning', 'تم إلغاء عملية الدفع');
            }
        }

        return redirect()->route('orders.index')
            ->with('warning', 'تم إلغاء عملية الدفع');
    }

    /**
     * Callback لـ Paymob
     */
    public function paymentCallbackPaymob(Request $request)
    {

        try {

            // جمع البيانات من query parameters
            $callbackData = $request->query();
            
            // تسجيل البيانات الكاملة للفحص
            Log::info('Paymob Callback Full Data', $callbackData);

            // استخراج معلومات الطلب
            $merchantOrderId = $callbackData['merchant_order_id'] ?? null;
            $success = filter_var($callbackData['success'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
            $pending = filter_var($callbackData['pending'] ?? 'true', FILTER_VALIDATE_BOOLEAN);
            $isAuth = filter_var($callbackData['is_auth'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
            $isCapture = filter_var($callbackData['is_capture'] ?? 'false', FILTER_VALIDATE_BOOLEAN);
            $transactionId = $callbackData['id'] ?? null;
            $amountCents = $callbackData['amount_cents'] ?? 0;
            $amount = $amountCents / 100; // تحويل من سنتس إلى العملة

            // التحقق من وجود معرف الطلب
            if (!$merchantOrderId) {
                Log::channel('payment')->error('Missing merchant_order_id in Paymob callback', $callbackData);
            }

            // البحث عن الطلب
            $order = Order::find($merchantOrderId);
            if (!$order) {
                Log::channel('payment')->error('Order not found for Paymob callback', [
                    'merchant_order_id' => $merchantOrderId,
                ]);
            }

            // التحقق من حالة الدفع
            if ($order->isPaid()) {
                Log::channel('payment')->info('Order already paid, redirecting', [
                    'order_id' => $order->id,
                ]);
            }

            // بناء بيانات التحقق
            $verificationData = [
                'id' => $transactionId,
                'merchant_order_id' => $merchantOrderId,
                'success' => $success,
                'pending' => $pending,
                'is_auth' => $isAuth,
                'is_capture' => $isCapture,
                'amount_cents' => $amountCents,
                'amount' => $amount,
                'data' => $callbackData,
            ];

            Log::channel('payment')->info('Processing Paymob callback verification', [
                'order_id' => $order->id,
                'success' => $success,
                'is_capture' => $isCapture,
                'amount' => $amount,
            ]);

            // معالجة الدفع بناءً على الحالة
            if ($success && $isCapture) {
                // الدفع ناجح ومستلم
                $this->processSuccessfulPayment($order, $verificationData);
 
                    
            } elseif ($success && !$isCapture && $pending) {
                // الدفع معلق (لم يتم الاستلام بعد)
                $this->processPendingPayment($order, $verificationData);
                
                    
            } else {
                // الدفع فاشل
                $this->processFailedPayment($order, $verificationData);

            }

        } catch (\Exception $e) {
            Log::channel('payment')->error('Paymob callback processing failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all(),
            ]);
        }
    }
 
    /**
     * معالجة الدفع الناجح
     */
    private function processSuccessfulPayment(Order $order, array $paymentData): void
    {
        try {
            // تحديث حالة الطلب
            $order->update([
                'payment_status' => Order::PAYMENT_STATUS_PAID,
                'payment_transaction_id' => $paymentData['id'] ?? null,
                'payment_method' => 'card',
                'payment_gateway' => 'paymob',
                'paid_at' => now(),
                'payment_details' => array_merge(
                    $order->payment_details ?? [],
                    [
                        'paymob_callback' => $paymentData,
                        'verified_at' => now(),
                        'status' => 'completed',
                        'amount_paid' => $paymentData['amount'] ?? 0,
                    ]
                ),
            ]);
 
            // إرسال إشعار بالدفع الناجح
            $this->sendPaymentSuccessNotification($order);

            Log::channel('payment')->info('Paymob payment processed successfully', [
                'order_id' => $order->id,
                'transaction_id' => $paymentData['id'],
                'amount' => $paymentData['amount'],
            ]);

        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to process successful payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * معالجة الدفع المعلق
     */
    private function processPendingPayment(Order $order, array $paymentData): void
    {
        try {
            $order->update([
                'payment_status' => Order::PAYMENT_STATUS_PENDING,
                'payment_transaction_id' => $paymentData['id'] ?? null,
                'payment_method' => 'card',
                'payment_gateway' => 'paymob',
                'payment_details' => array_merge(
                    $order->payment_details ?? [],
                    [
                        'paymob_callback' => $paymentData,
                        'status' => 'pending',
                        'pending_at' => now(),
                        'amount' => $paymentData['amount'] ?? 0,
                    ]
                ),
            ]);

            Log::channel('payment')->info('Paymob payment marked as pending', [
                'order_id' => $order->id,
                'transaction_id' => $paymentData['id'],
            ]);

        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to process pending payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * معالجة الدفع الفاشل
     */
    private function processFailedPayment(Order $order, array $paymentData): void
    {
        try {
            $order->update([
                'payment_status' => Order::PAYMENT_STATUS_FAILED,
                'payment_details' => array_merge(
                    $order->payment_details ?? [],
                    [
                        'paymob_callback' => $paymentData,
                        'failed_at' => now(),
                        'failure_reason' => 'Payment declined or failed',
                        'status' => 'failed',
                    ]
                ),
            ]);

            Log::channel('payment')->warning('Paymob payment failed', [
                'order_id' => $order->id,
                'payment_data' => $paymentData,
            ]);

        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to process failed payment', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * إرسال إشعار بنجاح الدفع
     */
    private function sendPaymentSuccessNotification(Order $order): void
    {
        try {
            if ($order->user) {
                // إرسال إشعار داخلي
                // $order->user->notify(new PaymentSuccessful($order));
                
                // يمكنك إضافة إشعار Firebase هنا إذا كان متوفراً
                // $this->firebaseService->sendToDevice(...)
            }

            Log::channel('payment')->info('Payment success notification sent', [
                'order_id' => $order->id,
                'user_id' => $order->user_id,
            ]);

        } catch (\Exception $e) {
            Log::channel('payment')->error('Failed to send payment success notification', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * إضافة route لهذا الـ callback في routes/api.php:
     * 
     * Route::get('/payment/callback/paymob', [PaymentController::class, 'paymentCallbackPaymob'])
     *     ->name('payment.callback.paymob');
     * 
     * وفي Paymob dashboard، اضبط Callback URL ليكون:
     * https://yourdomain.com/api/v1/payment/callback/paymob
     * 
     * أو للـ success/cancel URLs:
     * Success URL: https://yourdomain.com/api/v1/payment/success/paymob
     * Cancel URL: https://yourdomain.com/api/v1/payment/cancel/paymob
     */
}