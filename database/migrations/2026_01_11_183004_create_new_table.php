<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        // إنشاء جدول الدول
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name_ar');
            $table->string('name_en');
            $table->string('name_urdu');

            $table->string('code', 3)->unique();
            $table->string('dial_code', 10);
            $table->string('flag_emoji')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });

        // تحديث جدول السائقين
        Schema::table('drivers', function (Blueprint $table) {

            // الجنسية
            $table->enum('citizenship', ['saudi', 'resident'])->nullable()->after('user_id');
            $table->foreignId('country_id')->nullable()->constrained()->nullOnDelete();

            // بيانات شخصية
            // $table->date('date_of_birth')->nullable();
            // $table->string('national_id')->nullable(); // سعودي
            $table->string('iqama_number')->nullable(); // مقيم
            $table->date('iqama_expiry_date')->nullable();

            // صورة شخصية
            $table->string('personal_photo')->nullable();

            // صور الهوية
            $table->string('id_image_front')->nullable();
            $table->string('id_image_back')->nullable();

            // رخصة القيادة
            //$table->string('license_number')->nullable();
            $table->date('license_expiry_date')->nullable();
            $table->string('license_image_front')->nullable();
            $table->string('license_image_back')->nullable();

            // المركبة
            $table->string('vehicle_size')->nullable();
            $table->boolean('is_vehicle_owner')->default(false);

            $table->string('vehicle_plate_number')->nullable();
            $table->string('vehicle_registration_number')->nullable();
            $table->string('vehicle_residency_number')->nullable(); // إقامة مالك المركبة
            $table->string('vehicle_model')->nullable();
            $table->year('vehicle_year')->nullable();

            // صورة رخصة السير
            $table->string('vehicle_registration_image')->nullable();

            // حالة التحقق
            $table->boolean('is_verified')->default(false);
            $table->timestamp('verified_at')->nullable();
            $table->text('rejection_reason')->nullable();
        });

        // إنشاء جدول المركبات
        // Schema::create('vehicles', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
        //     $table->enum('type', ['truck', 'van', 'pickup', 'other']);
        //     $table->string('model');
        //     $table->year('year');
        //     $table->string('color');
        //     $table->string('plate_number')->unique();
        //     $table->string('registration_number')->unique();
        //     $table->string('registration_image')->nullable();
        //     $table->string('insurance_number')->nullable();
        //     $table->date('insurance_expiry_date')->nullable();
        //     $table->integer('capacity_liters')->nullable();
        //     $table->boolean('has_pump')->default(false);
        //     $table->boolean('has_cooling_system')->default(false);
        //     $table->boolean('is_active')->default(true);
        //     $table->enum('status', ['active', 'inactive', 'under_maintenance'])->default('active');
        //     $table->timestamps();

        //     $table->index(['driver_id', 'is_active']);
        // });

        // إنشاء جدول إشعارات المسؤول
        Schema::create('admin_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->enum('type', [
                'driver_registration',
                'order_issue',
                'payment_issue',
                'system_alert',
                'other'
            ]);
            $table->json('data')->nullable();
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });

        // إضافة حقل type إلى جدول users
        Schema::table('users', function (Blueprint $table) {
            $table->enum('type', ['user', 'driver', 'admin'])->default('user')->after('email');
        });
    }

    public function down()
    {
        Schema::dropIfExists('admin_notifications');
        Schema::dropIfExists('vehicles');

        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn([
                'citizenship',
                'country_id',
                'vehicle_size',
                'is_vehicle_owner',
                'id_image',
                'id_image_back',
                'license_image',
                'license_image_back',
                'vehicle_registration_image',
                'vehicle_plate_number',
                'vehicle_registration_number',
                'vehicle_residency_number',
                'vehicle_model',
                'vehicle_year',
                'vehicle_color',
                'vehicle_type',
                'driving_experience_years',
                'is_smoker',
                'has_helper',
                'helper_count',
                'insurance_number',
                'insurance_expiry_date',
                'bank_name',
                'iban_number',
                'emergency_contact_name',
                'emergency_contact_phone',
                'preferred_working_hours',
                'max_daily_orders',
                'radius_km',
                'is_verified',
                'verified_at',
                'verification_notes',
                'rejection_reason'
            ]);
        });

        Schema::dropIfExists('countries');

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
