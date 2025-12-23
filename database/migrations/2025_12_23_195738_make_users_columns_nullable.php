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
            $table->timestamp('email_verified_at')->nullable()->change();
            $table->string('password')->nullable()->change();

            $table->string('google_id')->nullable()->change();
            $table->string('facebook_id')->nullable()->change();

            $table->string('phone_number')->nullable()->change();
            $table->string('country_code')->nullable()->change();
            $table->string('full_phone')->nullable()->change();

            $table->timestamp('phone_verified_at')->nullable()->change();

            $table->string('otp')->nullable()->change();
            $table->timestamp('otp_expires_at')->nullable()->change();

            $table->rememberToken()->nullable()->change();

            $table->timestamp('created_at')->nullable()->change();
            $table->timestamp('updated_at')->nullable()->change();
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
        });
    }
};