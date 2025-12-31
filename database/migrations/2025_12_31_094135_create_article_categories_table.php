<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->nullable();
            $table->string('color')->nullable()->default('#3498db');
            $table->text('description')->nullable();
            $table->integer('parent_id')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_active')->default(true);
            $table->boolean('show_in_menu')->default(true);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->timestamps();

            $table->index(['parent_id', 'order']);
            $table->index('is_active');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_categories');
    }
};
