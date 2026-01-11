<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // جدول مواقع السائقين
        Schema::create('driver_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->float('accuracy')->nullable();
            $table->float('speed')->nullable();
            $table->float('heading')->nullable();
            $table->float('altitude')->nullable();
            $table->string('address')->nullable();
            $table->float('battery_level')->nullable();
            $table->boolean('is_moving')->default(true);
            $table->timestamp('device_timestamp')->nullable();
            $table->timestamp('estimated_arrival_time')->nullable();
            $table->float('distance_to_destination')->nullable();
            $table->timestamps();

            $table->index(['driver_id', 'order_id']);
            $table->index(['latitude', 'longitude']);
        });

        // جدول تقييمات الطلبات
        Schema::create('order_ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->foreignId('driver_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('rated_by', ['user', 'driver']);
            $table->integer('rating')->checkBetween([1, 5]);
            $table->text('comment')->nullable();
            $table->json('aspects')->nullable();
            $table->timestamps();

            $table->unique(['order_id', 'rated_by']);
            $table->index(['driver_id', 'rated_by']);
        });

        // جدول سجل تغييرات حالة الطلب
        Schema::create('order_status_history', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->integer('old_status_id');
            $table->integer('new_status_id');
            $table->foreignId('changed_by')->nullable()->constrained('users')->onDelete('set null');
            $table->string('changed_by_type')->default('system'); // system, user, driver, admin
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['order_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_status_history');
        Schema::dropIfExists('order_ratings');
        Schema::dropIfExists('driver_locations');
    }
};
