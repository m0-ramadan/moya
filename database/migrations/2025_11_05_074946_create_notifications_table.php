<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();

            // المستخدم أو الجهة اللي جايلها الإشعار
            $table->morphs('notifiable'); // notifiable_id + notifiable_type

            // عنوان الإشعار
            $table->string('title')->nullable();

            // نص الإشعار
            $table->text('message')->nullable();

            // نوع الإشعار (مثلاً system, order, chat...)
            $table->string('type')->nullable();

            // بيانات إضافية (JSON)
            $table->json('data')->nullable();

            // حالة القراءة
            $table->boolean('is_read')->default(false);

            // وقت القراءة
            $table->timestamp('read_at')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};
