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
        Schema::create('driver_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id');
            $table->foreignId('user_id'); // اللي اشتكى
            $table->foreignId('order_id')->nullable();

            $table->foreignId('report_reason_id');
            $table->text('description')->nullable();
            $table->boolean('is_resolved')->default(false);
            $table->text('admin_note')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('driver_reports');
    }
};
