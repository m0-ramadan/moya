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
        Schema::create('drivers', function (Blueprint $table) {
            $table->id();

            // يجب أن يكون user_id مرجعاً لجدول users
            $table->foreignId('user_id')
                ->unique()  // كل user لديه سائق واحد فقط
                ->constrained()
                ->cascadeOnDelete();

            $table->string('full_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('id_number')->nullable();           // رقم الهوية
            $table->string('license_number')->nullable();      // رخصة القيادة
            $table->date('license_expiry')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('average_rating', 3, 2)->default(4.00);
            $table->integer('total_ratings')->default(0);
            $table->integer('total_orders')->default(0);

            // يمكن إضافة الحقول الأخرى التي تحتاجها
            //   $table->string('national_id')->nullable();
            // $table->date('date_of_birth')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('drivers');
    }
};
