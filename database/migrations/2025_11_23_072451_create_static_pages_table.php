<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        // Schema::create('static_pages', function (Blueprint $table) {
        //     $table->id();
        //     $table->string('title');
        //     $table->string('slug')->unique();
        //     $table->longText('content')->nullable();
        //     $table->string('meta_title')->nullable();
        //     $table->text('meta_description')->nullable();
        //     $table->text('meta_keywords')->nullable();
        //     $table->enum('status', ['active', 'inactive'])->default('active');
        //     $table->timestamps();
        // });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //Schema::dropIfExists('static_pages');
    }
};
