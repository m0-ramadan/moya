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
}
