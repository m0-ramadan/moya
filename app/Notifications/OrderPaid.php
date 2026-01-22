<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;

class OrderPaid extends Notification implements ShouldQueue
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
        return ['database', 'broadcast'];
    }

    /**
     * Get the database representation of the notification.
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'title' => 'تم دفع الطلب',
            'message' => 'تم دفع الطلب رقم #' . $this->order->order_number . ' يمكنك البدء بالتوصيل',
            'type' => 'order_paid',
            'order_id' => $this->order->id,
            'order_number' => $this->order->order_number,
            'amount' => $this->amount,
            'user_name' => $this->order->user->name ?? 'عميل',
            'user_phone' => $this->order->user->phone ?? '',
            'timestamp' => now()->toDateTimeString(),
            'link' => '/driver/orders/' . $this->order->id,
        ];
    }

    /**
     * Get the broadcast representation of the notification.
     */
    public function toBroadcast(object $notifiable): BroadcastMessage
    {
        return new BroadcastMessage([
            'title' => 'تم دفع الطلب',
            'message' => 'تم دفع الطلب رقم #' . $this->order->order_number . ' يمكنك البدء بالتوصيل',
            'type' => 'order_paid',
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
            'title' => 'تم دفع الطلب',
            'message' => 'تم دفع الطلب رقم #' . $this->order->order_number,
            'order_id' => $this->order->id,
            'amount' => $this->amount,
        ];
    }
}