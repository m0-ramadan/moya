<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_comments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('guest_name')->nullable();
            $table->string('guest_email')->nullable();
            $table->text('content');
            $table->integer('parent_id')->nullable();
            $table->integer('likes_count')->default(0);
            $table->integer('replies_count')->default(0);
            $table->string('status')->default('pending'); // pending, approved, rejected, spam
            $table->boolean('is_edited')->default(false);
            $table->string('ip_address')->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['article_id', 'status']);
            $table->index(['user_id', 'created_at']);
            $table->index(['parent_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_comments');
    }
};
