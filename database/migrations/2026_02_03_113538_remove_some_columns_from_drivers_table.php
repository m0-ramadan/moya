<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
Schema::table('drivers', function (Blueprint $table) {
    if (Schema::hasColumn('drivers', 'phone_number')) {
        $table->dropIndex(['phone_number']);
        $table->dropColumn('phone_number');
    }

    if (Schema::hasColumn('drivers', 'full_name')) {
        $table->dropColumn('full_name');
    }

    if (Schema::hasColumn('drivers', 'blood_type')) {
        $table->dropColumn('blood_type');
    }
});

    }

    public function down(): void
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('full_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->string('blood_type')->nullable();

            // إعادة الإندكس
            $table->index('phone_number');
        });
    }
};
