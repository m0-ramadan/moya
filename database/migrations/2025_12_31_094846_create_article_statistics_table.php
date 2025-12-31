<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_statistics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->integer('views')->default(0);
            $table->integer('unique_views')->default(0);
            $table->integer('likes')->default(0);
            $table->integer('comments')->default(0);
            $table->integer('shares')->default(0);
            $table->integer('bookmarks')->default(0);
            $table->decimal('avg_read_time', 5, 2)->default(0);
            $table->decimal('bounce_rate', 5, 2)->default(0);
            $table->timestamps();

            $table->unique(['article_id', 'date']);
            $table->index('date');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_statistics');
    }
};
