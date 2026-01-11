<?php

namespace App\Events;

use App\Models\Wallet\LedgerEntry;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WalletEvent
{
    use Dispatchable, SerializesModels;

    public LedgerEntry $entry;
    public string $action;
    public array $metadata;

    public function __construct(LedgerEntry $entry, string $action = 'created', array $metadata = [])
    {
        $this->entry = $entry;
        $this->action = $action;
        $this->metadata = $metadata;
    }

    /**
     * Get event name
     */
    public function getName(): string
    {
        return "wallet.{$this->entry->owner_type}.{$this->action}";
    }

    /**
     * Get event data
     */
    public function getData(): array
    {
        return [
            'entry_id' => $this->entry->id,
            'owner_type' => $this->entry->owner_type,
            'owner_id' => $this->entry->owner_id,
            'type' => $this->entry->type,
            'amount' => $this->entry->amount,
            'balance_before' => $this->entry->balance_before,
            'balance_after' => $this->entry->balance_after,
            'reference' => $this->entry->reference,
            'status' => $this->entry->status,
            'action' => $this->action,
            'metadata' => $this->metadata,
            'timestamp' => now()->toISOString()
        ];
    }
}
