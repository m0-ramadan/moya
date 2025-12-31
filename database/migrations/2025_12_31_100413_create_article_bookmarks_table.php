<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('folder')->default('default');
            $table->json('tags')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['article_id', 'user_id']);
            $table->index(['user_id', 'folder']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_bookmarks');
    }
};
