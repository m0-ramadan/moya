<?php

namespace App\Listeners;

use App\Events\WalletEvent;
use App\Models\Wallet\LedgerEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class SendTransactionNotification implements ShouldQueue
{
    /**
     * Handle the event
     */
    public function handle(WalletEvent $event): void
    {
        $entry = $event->entry;

        // Only send notifications for completed transactions
        if ($entry->status !== LedgerEntry::STATUS_COMPLETED) {
            return;
        }

        // Determine notification type based on entry type
        $notificationType = $this->getNotificationType($entry);

        // Get owner
        $owner = $entry->owner;

        if (!$owner) {
            return;
        }

        // Prepare notification data
        $data = $this->prepareNotificationData($entry);

        // Send notification
        $this->sendNotification($owner, $notificationType, $data);
    }

    /**
     * Determine notification type
     */
    private function getNotificationType(LedgerEntry $entry): string
    {
        $types = [
            LedgerEntry::TYPE_DEPOSIT => 'deposit_completed',
            LedgerEntry::TYPE_WITHDRAWAL => 'withdrawal_completed',
            LedgerEntry::TYPE_CASHOUT => 'cashout_completed',
            LedgerEntry::TYPE_TRANSFER_IN => 'transfer_received',
            LedgerEntry::TYPE_TRANSFER_OUT => 'transfer_sent',
            LedgerEntry::TYPE_EARNING => 'earning_received',
            LedgerEntry::TYPE_PAYMENT => 'payment_made',
            LedgerEntry::TYPE_REFUND => 'refund_received'
        ];

        return $types[$entry->type] ?? 'transaction_completed';
    }

    /**
     * Prepare notification data
     */
    private function prepareNotificationData(LedgerEntry $entry): array
    {
        $direction = $entry->getDirectionAttribute();

        return [
            'entry_id' => $entry->id,
            'type' => $entry->type,
            'amount' => $entry->amount,
            'currency' => 'SAR', // Get from wallet
            'balance_before' => $entry->balance_before,
            'balance_after' => $entry->balance_after,
            'direction' => $direction,
            'description' => $entry->description,
            'reference' => $entry->reference,
            'timestamp' => $entry->created_at->toISOString()
        ];
    }

    /**
     * Send notification
     */
    private function sendNotification($owner, string $type, array $data): void
    {
        // Send via multiple channels
        try {
            // Email
            if ($owner->email && $owner->allow_notifications) {
                $this->sendEmailNotification($owner, $type, $data);
            }

            // Push notification
            if ($owner->allow_notifications) {
                $this->sendPushNotification($owner, $type, $data);
            }

            // SMS
            if ($owner->phone && $owner->allow_notifications) {
                $this->sendSmsNotification($owner, $type, $data);
            }
        } catch (\Exception $e) {
            Log::error('Failed to send transaction notification', [
                'owner_type' => get_class($owner),
                'owner_id' => $owner->id,
                'type' => $type,
                'error' => $e->getMessage()
            ]);
        }
    }

    private function sendEmailNotification($owner, string $type, array $data): void
    {
        // Implement email sending
    }

    private function sendPushNotification($owner, string $type, array $data): void
    {
        // Implement push notification
    }

    private function sendSmsNotification($owner, string $type, array $data): void
    {
        // Implement SMS sending
    }
}
