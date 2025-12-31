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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained()->cascadeOnDelete();
            $table->string('plate_number')->unique();
            $table->string('brand')->nullable();        // تويوتا، هيونداي...
            $table->string('model')->nullable();
            $table->string('color')->nullable();
            $table->string('type')->default('car');     // car / motorcycle / van ...
            $table->year('year')->nullable();
            $table->boolean('is_approved')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
