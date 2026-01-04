<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('drivers', function (Blueprint $table) {

            // بيانات الهوية
            $table->string('national_id')->unique()->after('phone');
            $table->date('date_of_birth')->after('national_id');

            // بيانات إضافية
            $table->string('blood_type')->nullable();

            // بيانات الرخصة (بديل license_expiry)
            $table->date('issue_date')->nullable();
            $table->date('expiry_date')->nullable();

            // حالة السائق
            $table->enum('status', ['saudi', 'resident', 'gulf', 'visitor'])
                  ->default('resident');

            // صورة + إشعارات
            $table->string('photo')->nullable();
            $table->boolean('allow_notifications')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'national_id',
                'date_of_birth',
                'blood_type',
                'issue_date',
                'expiry_date',
                'status',
                'photo',
                'allow_notifications',
            ]);
        });
    }
};
