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
        Schema::create('driver_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id');
            $table->foreignId('user_id'); // الزبون اللي قيّم
            $table->foreignId('order_id');       // اختياري
            $table->decimal('rating', 3, 2);               // 1.0 → 5.0
            $table->text('comment')->nullable();
            $table->timestamps();

            // $table->unique(['user_id', 'driver_id', 'order_id']); // منع التقييم المتكرر
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_ratings');
    }
};
