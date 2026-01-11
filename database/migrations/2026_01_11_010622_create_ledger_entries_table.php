<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ledger entries
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->enum('wallet_type', ['user', 'driver']);
            $table->unsignedBigInteger('wallet_id');
            $table->enum('owner_type', ['user', 'driver', 'system']);
            $table->unsignedBigInteger('owner_id')->nullable();
            $table->enum('type', [
                'deposit',
                'deposit_pending',
                'withdrawal',
                'transfer_in',
                'transfer_out',
                'payment',
                'hold',
                'release',
                'refund',
                'fee',
                'earning',
                'cashout',
                'commission',
                'payout',
                'adjustment'
            ]);
            $table->decimal('amount', 15, 2);
            $table->decimal('balance_before', 15, 2);
            $table->decimal('balance_after', 15, 2);
            $table->decimal('available_balance_before', 15, 2);
            $table->decimal('available_balance_after', 15, 2);
            $table->string('payment_method')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['pending', 'processing', 'completed', 'failed', 'cancelled', 'approved'])->default('pending');
            $table->string('reference')->unique();
            $table->unsignedBigInteger('related_entry_id')->nullable();
            $table->string('related_owner_type')->nullable();
            $table->unsignedBigInteger('related_owner_id')->nullable();
            $table->json('metadata')->nullable();
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users');
            $table->timestamps();

            // Indexes
            $table->index(['wallet_type', 'wallet_id']);
            $table->index(['owner_type', 'owner_id']);
            $table->index(['type', 'status']);
            $table->index('reference');
            $table->index('payment_transaction_id');
            $table->index('created_at');
            $table->index(['wallet_type', 'wallet_id', 'created_at']);

            // Foreign key for related entry
            $table->foreign('related_entry_id')->references('id')->on('ledger_entries');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
