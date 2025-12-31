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
        Schema::create('report_reasons', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();           // late_delivery, bad_behavior...
            $table->string('name_ar');                  // التأخير في التوصيل
            $table->string('name_en');                  // Late Delivery
            $table->boolean('needs_description')->default(false);
            $table->integer('order')->default(10);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_reasons');
    }
};
