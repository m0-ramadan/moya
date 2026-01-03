<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('contract_number')->unique();
            $table->enum('contract_type', ['individual', 'company']);
            $table->string('company_name')->nullable();
            $table->string('applicant_name')->nullable();
            $table->enum('duration_type', ['monthly', 'quarterly', 'semi_annual', 'annual']);
            $table->date('start_date');
            $table->date('end_date');
            $table->date('renewal_date')->nullable();
            $table->integer('total_orders_limit');
            $table->integer('remaining_orders');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('remaining_amount', 10, 2);
            $table->enum('status', ['active', 'expired', 'pending', 'cancelled'])->default('pending');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
