<?php

namespace App\Http\Controllers\Api\Orders;

use App\Events\Order\TripStartedForDriver;
use App\Events\Order\TripStartedForUser;
use App\Http\Controllers\Controller;
use App\Http\Resources\WebsiteUser\OrderResource;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Notifications\PaymentSuccessful;
use App\Services\CouponService;
use App\Services\FirebaseNotificationService;
use App\Services\Payment\PaymentService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use Illuminate\Validation\ValidationException;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    private PaymentService $paymentService;

    private CouponService $couponService;

    private $firebaseService;

    public function __construct(
        PaymentService $paymentService,
        FirebaseNotificationService $firebaseService,
        CouponService $couponService
    )
    {
        $this->paymentService = $paymentService;
        $this->firebaseService = $firebaseService;
        $this->couponService = $couponService;

    }

    /**
     * بدء عملية الدفع
     */
    public function initiatePayment(Request $request, Order $order)
    {
        $request->validate([
            'offer_id' => 'required|exists:order_offers,id',
            'gateway' => 'required|in:wallet,paymob,tamara,tabby,cash_on_delivery',
            'payment_method' => 'required|string',
            'save_card' => 'nullable|boolean',
            'coupon_code' => 'nullable|string|max:50',
        ]);

        try {
            $user = $request->user();
            $paymentUrl = null;
            $this->authorizeOrderOwner($order, $user->id);

            $result = DB::transaction(function () use ($request, $order, $user, &$paymentUrl) {

                // $offer = $order->offers()
                //     ->where('id', $request->offer_id)
                //     ->lockForUpdate()
                //     ->firstOrFail();
                if ($order->isExpired() || $order->status?->name === 'expired') {
                    throw new \Exception('Order has expired');
                }
                $offer = $order->offers()
                    ->where('id', $request->offer_id)
                    ->where(function ($q) {
                        $q->whereNull('expires_at')
                            ->orWhere('expires_at', '>', now());
                    })
                    ->lockForUpdate()
                    ->firstOrFail();

                $this->syncCouponState($request, $order, (float) $offer->price);

                $offer->update(['status' => 'payment_pending']);

                if (! in_array($offer->status, ['accepted', 'payment_pending'])) {
                    throw new \Exception('Offer must be accepted before payment');
                }

                if ($order->payment_status === Order::PAYMENT_STATUS_PAID) {
                    throw new \Exception('Order already paid');
                }

                $gateway = $request->gateway;

                // الدفع عند الاستلام
                if ($gateway === 'cash_on_delivery') {
                    $status = OrderStatus::where('name', 'in-road')->first();

                    $order->update([
                        'payment_status' => Order::PAYMENT_STATUS_PENDING,
                        'order_status_id' => $status->id,
                        'payment_method' => 'cash_on_delivery',
                        'payment_gateway' => 'cash_on_delivery',
                        'payment_transaction_id' => null,
                        'driver_id' => $offer->driver_id,
                        'payment_details' => json_encode([
                            'success' => true,
                            'gateway' => 'cash_on_delivery',
                            'method' => 'cash_on_delivery',
                            'message' => 'Payment will be collected on delivery',
                        ]),
                        'expires_at' => null,
                    ]);
                    $this->couponService->recordUsage($order, $request);
                    $order->offers()
                        ->whereIn('status', ['accepted', 'payment_pending'])
                        ->update([
                            'status' => 'accepted',
                        ]);
                    // رفض كل العروض ما عدا المقبول أو المعلق للدفع
                    $order->offers()
                        ->whereNotIn('status', ['accepted', 'payment_pending'])
                        ->update([
                            'status' => 'rejected',
                        ]);

                    event(new TripStartedForDriver($order));

                    event(new TripStartedForUser($order));
                    if ($offer->driver_id) {
                        $this->firebaseService->sendToDriver(
                            $offer->driver_id,
                            [
                                'title' => 'تم بدء الرحلة',
                                'message' => 'تم تعيين الطلب لك، توجه إلى العميل الآن',
                                'type' => 'trip_started',
                                'data' => [
                                    'order_id' => $order->id,
                                    'offer_id' => $offer->id,
                                ],
                            ]
                        );
                    }
                    $paymentUrl = null;

                    return [
                        'success' => true,
                        'payment' => json_decode($order->payment_details, true),
                    ];
                }

                // إعادة استخدام session قديمة إذا كانت صالحة
                if (
                    $order->payment_status === Order::PAYMENT_STATUS_PROCESSING &&
                    $order->expires_at &&
                    now()->lt($order->expires_at)
                ) {
                    $paymentData = json_decode($order->payment_details, true)['payment_data'] ?? null;

                    if (
                        $paymentData &&
                        $paymentData['gateway'] === $gateway &&
                        $paymentData['method'] === $request->payment_method
                    ) {
                        $paymentUrl = $this->extractPaymentUrl($paymentData, $request->gateway);

                        return [
                            'success' => true,
                            'payment' => $paymentData,
                        ];
                    }
                }

                // بيانات إضافية للبوابة
                $additionalData = [
                    'save_card' => $request->boolean('save_card'),
                    'installments' => $request->input('installments'),
                    'metadata' => $request->only(['device_id', 'ip_address']),
                ];

                // معالجة الدفع عبر service
                $paymentResult = $this->paymentService->processOrderPayment(
                    $user,
                    $order,
                    $offer,
                    $gateway,
                    $request->payment_method,
                    $additionalData
                );

                if (! $paymentResult['success']) {
                    throw new \Exception($paymentResult['message']);
                }

                $paymentData = $paymentResult['payment'];

                // استخراج رابط الدفع حسب البوابة
                $paymentUrl = $this->extractPaymentUrl($paymentData, $request->gateway);

                // تحديث الطلب
                $order->update([
                    'payment_status' => Order::PAYMENT_STATUS_PROCESSING,
                    'payment_method' => $request->payment_method,
                    'payment_gateway' => $gateway,
                    'payment_transaction_id' => $paymentData['payment_id'] ?? null,
                    'payment_details' => json_encode($paymentResult),
                    'expires_at' => $paymentData['expires_at'] ?? null,
                ]);

                return $paymentResult;
            });

            return $this->successResponse([
                'order' => new OrderResource($order->fresh()),
                'payment_url' => $paymentUrl,
                'coupon' => $order->fresh()->coupon_code ? [
                    'code' => $order->fresh()->coupon_code,
                    'discount_amount' => $order->fresh()->getResolvedDiscountAmount(),
                ] : null,
                'payment' => $result,
            ], 'Payment initiated successfully');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors(), $e->getMessage(), 422);
        } catch (\Throwable $e) {
            Log::channel('payment')->error('Payment initiation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);

            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    /**
     * استخراج رابط الدفع حسب بوابة الدفع
     */
    private function extractPaymentUrl(array $paymentData, string $gateway): ?string
    {

        // حاول الحصول على الـ gateway من أي مكان

        // Tamara
        if ($gateway === 'tamara') {
            return $paymentData['checkout_url']
                ?? $paymentData['payment']['checkout_url']
                ?? $paymentData['payment_data']['checkout_url']
                ?? null;
        }

        // Tabby
        if ($gateway === 'tabby') {
            return $paymentData['raw_response']['configuration']['available_products']['installments'][0]['web_url']
                ?? null;
        }

        // Paymob
        if ($gateway === 'paymob') {
            return $paymentData['payment_url']
                ?? $paymentData['payment']['payment_url']
                ?? $paymentData['payment_data']['payment_url']
                ?? null;
        }

        return null;
    }

    /**
     * التحقق من حالة الدفع
     */
    public function checkPaymentStatus(Order $order)
    {
        if (auth()->id() !== $order->user_id) {
            return $this->errorResponse('هذا الطلب غير مصرح لك بالوصول إليه.', 403);
        }

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
                'amounts' => [
                    'original_amount' => $order->getResolvedOriginalAmount(),
                    'discount_amount' => $order->getResolvedDiscountAmount(),
                    'final_amount' => $order->getResolvedFinalAmount(),
                ],
                'coupon' => $order->coupon_code ? [
                    'code' => $order->coupon_code,
                    'type' => $order->coupon_type,
                    'value' => (float) ($order->coupon_value ?? 0),
                ] : null,
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
            if (! $order->isPaid()) {
                return $this->errorResponse('Order is not paid', 400);
            }

            $result = $this->paymentService->refundPayment(
                $order,
                $request->input('reason')
            );

            if (! $result['success']) {
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
    public function getPaymentMethods(Request $request)
    {
        try {
            $type = $request->input('type', 'orders');
            // default orders لو مش متبعت

            $gateways = $this->paymentService->getAvailableGateways();

            // 🔥 فلترة حسب النوع
            if ($type === 'wallet') {
                unset($gateways['wallet']);
                unset($gateways['tamara']);
                unset($gateways['tabby']);
                unset($gateways['cash_on_delivery']);
            }

            $availableMethods = [];

            foreach ($gateways as $gatewayKey => $gatewayInfo) {
                $availableMethods[] = [
                    'id' => $gatewayKey,
                    'name' => $gatewayInfo['name'],
                    'image' => $gatewayInfo['image'] ?? null,
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
        $orderId = $request->input('payment_id');

        if (! $orderId) {
            return response()->json([
                'status' => false,
                'message' => 'بيانات الدفع غير صحيحة',
            ], 400);
        }

        $order = Order::where('payment_transaction_id', $orderId)->first();

        if (! $order) {
            return response()->json([
                'status' => false,
                'message' => 'الطلب غير موجود',
            ], 404);
        }

        try {
            // التحقق من الدفع
            $this->paymentService->verifyPayment($order);
            $status = OrderStatus::where('name', 'in-road')->first();

            DB::transaction(function () use ($order, $status) {

                // جلب العرض المقبول أو المعلق للدفع
                $acceptedOffer = $order->offers()
                    ->whereIn('status', ['accepted', 'payment_pending'])
                    ->first();

                // تحديث الطلب
                $order->update([
                    'order_status_id' => $status->id,

                    'payment_status' => Order::PAYMENT_STATUS_PAID,
                    'paid_at' => now(),
                    'driver_id' => $acceptedOffer?->driver_id,
                ]);
                $order->offers()
                    ->whereIn('status', ['accepted', 'payment_pending'])
                    ->update([
                        'status' => 'accepted',
                    ]);
                // رفض كل العروض ما عدا المقبول أو المعلق للدفع
                $order->offers()
                    ->whereNotIn('status', ['accepted', 'payment_pending'])
                    ->update([
                        'status' => 'rejected',
                    ]);

                $this->couponService->recordUsage($order);
            });

            event(new TripStartedForDriver($order));
            event(new TripStartedForUser($order));

            return response()->json([
                'status' => true,
                'message' => 'تم الدفع بنجاح، الطلب قيد التوصيل، وباقي العروض تم رفضها',
                'order' => new OrderResource($order->fresh()),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'جاري معالجة الدفع، سيتم تحديث الحالة قريباً',
                'error' => $e->getMessage(),
            ], 500);
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
            if (! $merchantOrderId) {
                Log::channel('payment')->error('Missing merchant_order_id in Paymob callback', $callbackData);
            }

            // البحث عن الطلب
            $order = Order::find($merchantOrderId);
            if (! $order) {
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
            } elseif ($success && ! $isCapture && $pending) {
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

            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment callback',
            ], 500);
        }
    }

    public function applyCoupon(Request $request, Order $order)
    {
        $request->validate([
            'offer_id' => 'required|exists:order_offers,id',
            'coupon_code' => 'required|string|max:50',
        ]);

        try {
            $this->authorizeOrderOwner($order, $request->user()->id);

            $offer = $order->offers()
                ->where('id', $request->offer_id)
                ->firstOrFail();

            $summary = $this->couponService->applyCouponToOrder(
                $order,
                $request->user(),
                (float) $offer->price,
                $request->coupon_code
            );

            return $this->successResponse([
                'order' => new OrderResource($order->fresh(['coupon'])),
                'coupon' => $summary['coupon'],
                'amounts' => [
                    'original_amount' => $summary['original_amount'],
                    'discount_amount' => $summary['discount_amount'],
                    'final_amount' => $summary['final_amount'],
                ],
            ], 'تم تطبيق الكوبون بنجاح');
        } catch (ValidationException $e) {
            return $this->validationError($e->errors(), $e->getMessage(), 422);
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    public function removeCoupon(Request $request, Order $order)
    {
        $request->validate([
            'offer_id' => 'nullable|exists:order_offers,id',
        ]);

        try {
            $this->authorizeOrderOwner($order, $request->user()->id);

            $baseAmount = $this->resolveBaseAmount($order, $request->input('offer_id'));
            $summary = $this->couponService->clearCouponFromOrder($order, $baseAmount);

            return $this->successResponse([
                'order' => new OrderResource($order->fresh()),
                'coupon' => null,
                'amounts' => $summary,
            ], 'تم إزالة الكوبون من الطلب');
        } catch (\Throwable $e) {
            return $this->errorResponse($e->getMessage(), 500);
        }
    }

    private function authorizeOrderOwner(Order $order, ?int $userId): void
    {
        if ($userId === null || $order->user_id !== $userId) {
            throw ValidationException::withMessages([
                'order' => 'هذا الطلب غير مصرح لك بالوصول إليه.',
            ]);
        }
    }

    private function syncCouponState(Request $request, Order $order, float $baseAmount): void
    {
        if ($request->filled('coupon_code')) {
            $this->couponService->applyCouponToOrder(
                $order,
                $request->user(),
                $baseAmount,
                $request->coupon_code
            );

            return;
        }

        if ($order->coupon_id) {
            $this->couponService->revalidateAppliedCoupon($order, $request->user(), $baseAmount);

            return;
        }

        $this->couponService->clearCouponFromOrder($order, $baseAmount);
    }

    private function resolveBaseAmount(Order $order, ?string $offerId = null): float
    {
        if ($offerId) {
            $offer = $order->offers()->where('id', $offerId)->first();

            if ($offer) {
                return (float) $offer->price;
            }
        }

        if ($order->original_amount !== null) {
            return (float) $order->original_amount;
        }

        return (float) $order->getPaymentAmount();
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
