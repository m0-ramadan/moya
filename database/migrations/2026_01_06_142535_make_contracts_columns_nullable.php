<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable()->change();
            $table->string('contract_number')->nullable()->change();
            $table->string('company_name')->nullable()->change();
            $table->string('applicant_name')->nullable()->change();
            $table->string('phone')->nullable()->change();
            DB::statement("ALTER TABLE contracts MODIFY contract_type ENUM('individual','company') NULL");
            DB::statement("ALTER TABLE contracts MODIFY duration_type ENUM('monthly','quarterly','semi_annual','annual') NULL");
            DB::statement("ALTER TABLE contracts MODIFY status ENUM('active','expired','pending','cancelled') NULL");

            $table->date('start_date')->nullable()->change();
            $table->date('end_date')->nullable()->change();
            $table->date('renewal_date')->nullable()->change();
            $table->integer('total_orders_limit')->nullable()->change();
            $table->integer('remaining_orders')->nullable()->change();
            $table->decimal('total_amount', 10, 2)->nullable()->change();
            $table->decimal('paid_amount', 10, 2)->nullable()->change();
            $table->decimal('remaining_amount', 10, 2)->nullable()->change();
            $table->text('notes')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable(false)->change();
            $table->string('contract_number')->nullable(false)->change();
            $table->string('company_name')->nullable(false)->change();
            $table->string('applicant_name')->nullable(false)->change();
            $table->string('phone')->nullable(false)->change();

            DB::statement("ALTER TABLE contracts MODIFY contract_type ENUM('individual','company') NOT NULL");
            DB::statement("ALTER TABLE contracts MODIFY duration_type ENUM('monthly','quarterly','semi_annual','annual') NOT NULL");
            DB::statement("ALTER TABLE contracts MODIFY status ENUM('active','expired','pending','cancelled') NOT NULL");

            $table->date('start_date')->nullable(false)->change();
            $table->date('end_date')->nullable(false)->change();
            $table->integer('total_orders_limit')->nullable(false)->change();
            $table->integer('remaining_orders')->nullable(false)->change();
            $table->decimal('total_amount', 10, 2)->nullable(false)->change();
            $table->decimal('paid_amount', 10, 2)->nullable(false)->change();
            $table->decimal('remaining_amount', 10, 2)->nullable(false)->change();
        });
    }
};
