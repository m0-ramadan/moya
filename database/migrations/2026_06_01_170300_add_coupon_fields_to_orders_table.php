<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'coupon_id')) {
                $table->foreignId('coupon_id')->nullable()->after('contract_id')->constrained('coupons')->nullOnDelete();
            }

            if (! Schema::hasColumn('orders', 'coupon_code')) {
                $table->string('coupon_code')->nullable()->after('coupon_id');
            }

            if (! Schema::hasColumn('orders', 'coupon_type')) {
                $table->string('coupon_type')->nullable()->after('coupon_code');
            }

            if (! Schema::hasColumn('orders', 'coupon_value')) {
                $table->decimal('coupon_value', 10, 2)->nullable()->after('coupon_type');
            }

            if (! Schema::hasColumn('orders', 'original_amount')) {
                $table->decimal('original_amount', 10, 2)->nullable()->after('coupon_value');
            }

            if (! Schema::hasColumn('orders', 'discount_amount')) {
                $table->decimal('discount_amount', 10, 2)->default(0)->after('original_amount');
            }

            if (! Schema::hasColumn('orders', 'final_amount')) {
                $table->decimal('final_amount', 10, 2)->nullable()->after('discount_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $columns = array_filter([
                Schema::hasColumn('orders', 'final_amount') ? 'final_amount' : null,
                Schema::hasColumn('orders', 'discount_amount') ? 'discount_amount' : null,
                Schema::hasColumn('orders', 'original_amount') ? 'original_amount' : null,
                Schema::hasColumn('orders', 'coupon_value') ? 'coupon_value' : null,
                Schema::hasColumn('orders', 'coupon_type') ? 'coupon_type' : null,
                Schema::hasColumn('orders', 'coupon_code') ? 'coupon_code' : null,
            ]);

            if (Schema::hasColumn('orders', 'coupon_id')) {
                $table->dropConstrainedForeignId('coupon_id');
            }

            if (! empty($columns)) {
                $table->dropColumn($columns);
            }
        });
    }
};
