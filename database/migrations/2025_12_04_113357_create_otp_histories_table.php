<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('otp_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('phone_number');
            $table->string('otp');
            $table->string('purpose'); // login, register, reset
            $table->string('status')->default('pending'); // pending, verified, expired, failed
            $table->timestamp('expires_at');
            $table->ipAddress('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            
            $table->index(['phone_number', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('otp_histories');
    }
};