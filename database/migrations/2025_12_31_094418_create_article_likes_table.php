<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_likes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type')->default('like'); // like, love, haha, wow, sad, angry
            $table->timestamps();

            $table->unique(['article_id', 'user_id']);
            $table->index(['article_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_likes');
    }
};
