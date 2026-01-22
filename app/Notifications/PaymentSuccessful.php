<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Messages\DatabaseMessage;

class PaymentSuccessful extends Notification implements ShouldQueue
{
    use Queueable;

    public $order;
    public $amount;

    /**
     * Create a new notification instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
        $this->amount = $order->getPaymentAmount();
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database', 'broadcast', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('✅ تم تأكيد الدفع بنجاح - طلب #' . $this->order->order_number)
            ->greeting('مرحباً ' . $notifiable->name . '!')
            ->line('تم تأكيد الدفع بنجاح لطلبك رقم: #' . $this->order->order_number)
            ->line('مبلغ الدفع: ' . number_format($this->amount, 2) . ' ريال')
            ->line('طريقة الدفع: ' . $this->getPaymentMethodName())
            ->line('حالة الطلب: ' . $this->getOrderStatus())
            ->action('عرض تفاصيل الطلب', url('/orders/' . $this->order->id))
            ->line('شكراً لاستخدامك تطبيقنا!');
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تم تأكيد الدفع بنجاح',
            'message' => 'تم دفع مبلغ ' . number_format($this->amount, 2) . ' ريال لطلب رقم #' . $this->order->order_number,
            'type' => 'payment_success',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->amount,
            'payment_method' => $this->order->payment_method,
            'timestamp' => now()->toDateTimeString(),
            'link' => '/orders/' . $this->order->id,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'تم تأكيد الدفع بنجاح',
            'message' => 'تم دفع مبلغ ' . number_format($this->amount, 2) . ' ريال لطلب رقم #' . $this->order->order_number,
            'type' => 'payment_success',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'title' => 'تم تأكيد الدفع بنجاح',
            'message' => 'تم دفع مبلغ ' . number_format($this->amount, 2) . ' ريال لطلب رقم #' . $this->order->order_number,
            'order_id' => $this->order->id,
            'amount' => $this->amount,
            'payment_method' => $this->order->payment_method,
        ];
    }

    /**
     * Get payment method name in Arabic
     */
    private function getPaymentMethodName(): string
    {
        $methods = [
            Order::PAYMENT_METHOD_WALLET => 'المحفظة الإلكترونية',
            Order::PAYMENT_METHOD_CREDIT_CARD => 'بطاقة ائتمان',
            Order::PAYMENT_METHOD_MADA => 'مدى',
            Order::PAYMENT_METHOD_APPLE_PAY => 'Apple Pay',
            Order::PAYMENT_METHOD_PAYMOB => 'PayMob',
        ];

        return $methods[$this->order->payment_method] ?? $this->order->payment_method;
    }

    /**
     * Get order status in Arabic
     */
    private function getOrderStatus(): string
    {
        $statuses = [
            1 => 'قيد الانتظار',
            2 => 'مقبول',
            3 => 'قيد التنفيذ',
            4 => 'مكتمل',
            5 => 'ملغي',
        ];

        return $statuses[$this->order->order_status_id] ?? 'غير معروف';
    }
}