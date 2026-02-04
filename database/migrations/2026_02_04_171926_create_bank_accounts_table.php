<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bank_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');          // اسم البنك / المحفظة
            $table->string('image')->nullable(); // صورة اللوجو
            $table->boolean('is_active')->default(true);
            $table->enum('type', ['bank', 'wallet'])->default('bank');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bank_accounts');
    }
};
