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
Schema::create('section_contents', function (Blueprint $table) {
    $table->id();
    $table->foreignId('section_id')->constrained('page_sections')->cascadeOnDelete();

    $table->string('key'); 
    // title, subtitle, description, image, price, icon, value

    $table->longText('value')->nullable(); // text / json
    $table->unsignedInteger('order')->default(0);
    $table->boolean('is_active')->default(true);

    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_contents');
    }
};
