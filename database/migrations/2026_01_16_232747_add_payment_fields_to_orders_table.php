<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentFieldsToOrdersTable extends Migration
{
    public function up()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('payment_status')->default('pending')->after('order_status_id');
            $table->string('payment_method')->nullable()->after('payment_status');
            $table->string('payment_transaction_id')->nullable()->after('payment_method');
            $table->timestamp('paid_at')->nullable()->after('payment_transaction_id');
            $table->json('payment_details')->nullable()->after('paid_at');
        });
    }

    public function down()
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'payment_status',
                'payment_method',
                'payment_transaction_id',
                'paid_at',
                'payment_details',
            ]);
        });
    }
}
