<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('excerpt')->nullable();
            $table->longText('content');
            $table->text('summary')->nullable();
            $table->string('featured_image')->nullable();
            $table->string('reading_time')->nullable();
            $table->integer('views_count')->default(0);
            $table->integer('likes_count')->default(0);
            $table->integer('shares_count')->default(0);
            $table->integer('comments_count')->default(0);
            $table->string('status')->default('draft'); // draft, published, archived
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_sponsored')->default(false);
            $table->boolean('allow_comments')->default(true);
            $table->date('published_at')->nullable();
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->nullable();
            $table->json('meta_keywords')->nullable();
            $table->text('meta_description')->nullable();
            $table->json('tags')->nullable();
            $table->json('related_articles')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('articles');
    }
};
