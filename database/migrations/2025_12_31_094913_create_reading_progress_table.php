<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('reading_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->integer('progress_percentage')->default(0);
            $table->integer('last_position')->default(0);
            $table->timestamp('last_read_at')->nullable();
            $table->boolean('completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['article_id', 'user_id']);
            $table->index(['user_id', 'completed']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('reading_progress');
    }
};
