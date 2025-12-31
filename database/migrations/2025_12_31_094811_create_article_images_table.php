<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_images', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->string('image_path');
            $table->string('thumbnail_path')->nullable();
            $table->string('alt_text')->nullable();
            $table->string('caption')->nullable();
            $table->integer('width')->nullable();
            $table->integer('height')->nullable();
            $table->integer('size')->nullable();
            $table->string('format')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'order']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_images');
    }
};
