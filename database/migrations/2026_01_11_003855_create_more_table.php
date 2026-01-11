<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        /*
        |--------------------------------------------------------------------------
        | User Wallets
        |--------------------------------------------------------------------------
        */
        Schema::create('user_wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('held_balance', 15, 2)->default(0);

            // Generated column (must come AFTER held_balance)
            $table->decimal('available_balance', 15, 2)
                ->storedAs('balance - held_balance');

            $table->string('currency', 10)->default('SAR');
            $table->string('status', 20)->default('active');

            $table->timestamp('last_transaction_at')->nullable();

            $table->decimal('daily_limit', 15, 2)->default(10000);
            $table->decimal('monthly_limit', 15, 2)->default(50000);

            $table->decimal('total_deposits_today', 15, 2)->default(0);
            $table->decimal('total_withdrawals_today', 15, 2)->default(0);
            $table->decimal('total_transfers_today', 15, 2)->default(0);

            $table->unsignedInteger('version')->default(1);

            $table->timestamps();

            $table->index('status');
            $table->index('last_transaction_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Driver Wallets
        |--------------------------------------------------------------------------
        */
        Schema::create('driver_wallets', function (Blueprint $table) {
            $table->id();

            $table->foreignId('driver_id')
                ->unique()
                ->constrained()
                ->cascadeOnDelete();

            $table->decimal('balance', 15, 2)->default(0);
            $table->decimal('held_balance', 15, 2)->default(0);

            $table->decimal('available_balance', 15, 2)
                ->storedAs('balance - held_balance');

            $table->string('currency', 10)->default('SAR');
            $table->string('status', 20)->default('active');

            $table->timestamp('last_transaction_at')->nullable();

            $table->decimal('daily_limit', 15, 2)->default(20000);
            $table->decimal('monthly_limit', 15, 2)->default(100000);

            $table->decimal('total_earnings_today', 15, 2)->default(0);
            $table->decimal('total_withdrawals_today', 15, 2)->default(0);
            $table->decimal('total_cashouts_today', 15, 2)->default(0);

            $table->unsignedInteger('version')->default(1);

            $table->timestamps();

            $table->index('status');
            $table->index('last_transaction_at');
        });

        /*
        |--------------------------------------------------------------------------
        | Wallet Transactions
        |--------------------------------------------------------------------------
        */
        // Schema::create('wallet_transactions', function (Blueprint $table) {
        //     $table->id();

        //     // Polymorphic wallet reference
        //     $table->string('wallet_type', 20); // user | driver
        //     $table->unsignedBigInteger('wallet_id');

        //     $table->foreignId('user_id')
        //         ->nullable()
        //         ->constrained()
        //         ->nullOnDelete();

        //     $table->foreignId('driver_id')
        //         ->nullable()
        //         ->constrained()
        //         ->nullOnDelete();

        //     $table->string('type', 30);
        //     $table->decimal('amount', 15, 2);

        //     $table->decimal('balance_before', 15, 2);
        //     $table->decimal('balance_after', 15, 2);
        //     $table->decimal('available_balance_before', 15, 2);
        //     $table->decimal('available_balance_after', 15, 2);

        //     $table->string('payment_method')->nullable();
        //     $table->string('payment_transaction_id')->nullable();

        //     $table->text('description')->nullable();
        //     $table->string('status', 20)->default('pending');

        //     $table->string('reference')->unique();

        //     $table->foreignId('related_user_id')
        //         ->nullable()
        //         ->constrained('users')
        //         ->nullOnDelete();

        //     $table->foreignId('related_driver_id')
        //         ->nullable()
        //         ->constrained('drivers')
        //         ->nullOnDelete();

        //     $table->unsignedBigInteger('related_transaction_id')->nullable();

        //     $table->json('metadata')->nullable();

        //     $table->string('ip_address')->nullable();
        //     $table->string('user_agent')->nullable();

        //     $table->timestamp('expires_at')->nullable();
        //     $table->timestamp('processed_at')->nullable();
        //     $table->timestamp('approved_at')->nullable();

        //     $table->foreignId('approved_by')
        //         ->nullable()
        //         ->constrained('users')
        //         ->nullOnDelete();

        //     $table->timestamps();

        //     // Self reference
        //     $table->foreign('related_transaction_id')
        //         ->references('id')
        //         ->on('wallet_transactions')
        //         ->nullOnDelete();

        //     // Indexes
        //     $table->index(['wallet_type', 'wallet_id']);
        //     $table->index('type');
        //     $table->index('status');
        //     $table->index('created_at');
        // });

        /*
        |--------------------------------------------------------------------------
        | Idempotency Keys
        |--------------------------------------------------------------------------
        */
        Schema::create('idempotency_keys', function (Blueprint $table) {
            $table->id();

            $table->string('key');
            $table->string('request_hash')->nullable();
            $table->string('response_hash')->nullable();

            $table->string('status', 20)->default('processing');

            $table->timestamp('processed_at')->nullable();
            $table->timestamp('expires_at')->nullable(); // ✅ FIX

            $table->string('resource_type')->nullable();
            $table->unsignedBigInteger('resource_id')->nullable();
            $table->string('wallet_type', 20)->nullable();

            $table->timestamps();

            $table->unique(['key', 'wallet_type']);
            $table->index('expires_at');
        });
    }

    public function down()
    {
        Schema::dropIfExists('idempotency_keys');
        Schema::dropIfExists('wallet_transactions');
        Schema::dropIfExists('driver_wallets');
        Schema::dropIfExists('user_wallets');
    }
};
