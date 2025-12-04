<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_addresses', function (Blueprint $table) {
            $table->id();

            // ربط العنوان بالمستخدم
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // الاسم الشخصي
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();

            // تفاصيل المبنى
            $table->string('building')->nullable();
            $table->string('floor')->nullable();
            $table->string('apartment_number')->nullable();

            // تفاصيل العنوان الكاملة
            $table->text('address_details')->nullable();

            // اسم مختصر للعنوان (مثلاً: المنزل، العمل، الشاليه...)
            $table->string('label')->nullable();

            // رقم الهاتف
            $table->string('phone')->nullable();

            // الموقع الجغرافي
            $table->string('city')->nullable();
            $table->string('area')->nullable();

            // نوع العنوان (مثلاً: home, work, other)
            $table->string('type')->default('home');

            // ممكن تضيف إحداثيات في المستقبل
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_addresses');
    }
};
