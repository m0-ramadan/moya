<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('payment_attempts', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('order_id');

            $table->string('gateway')->nullable(); // paymob, hyperpay, stripe

            $table->string('reference_id')->unique();

            $table->string('payment_url')->nullable();

            $table->unsignedBigInteger('amount_cents')->nullable();

            $table->string('currency', 10)->default('EGP');

            $table->string('status')->default('pending');
            // pending | paid | failed | expired

            $table->json('gateway_response')->nullable();

            $table->timestamp('paid_at')->nullable();

            $table->timestamps();

            $table->index(['order_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payment_attempts');
    }
};
