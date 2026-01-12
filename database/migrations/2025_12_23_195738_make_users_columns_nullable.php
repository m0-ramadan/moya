<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            $table->string('name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->datetime('email_verified_at')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->string('google_id')->nullable()->change();
            $table->string('facebook_id')->nullable()->change();

            $table->string('phone_number')->nullable()->change();
            $table->string('country_code')->nullable()->change();
            $table->string('full_phone')->nullable()->change();

            $table->datetime('phone_verified_at')->nullable()->change();

            $table->string('otp')->nullable()->change();
            $table->datetime('otp_expires_at')->nullable()->change();

            $table->rememberToken()->nullable()->change();

            $table->datetime('created_at')->nullable()->change();
            $table->datetime('updated_at')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->string('country_code')->nullable(false)->change();
            $table->string('full_phone')->nullable(false)->change();

            // استعادة الأعمدة الأخرى إلى الحالة السابقة إذا أردت
            $table->datetime('email_verified_at')->nullable(false)->change();
            $table->datetime('phone_verified_at')->nullable(false)->change();
            $table->datetime('otp_expires_at')->nullable(false)->change();
            $table->datetime('created_at')->nullable(false)->change();
            $table->datetime('updated_at')->nullable(false)->change();
        });
    }
};
