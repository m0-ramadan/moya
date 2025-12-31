<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_views', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('ip_address');
            $table->string('user_agent')->nullable();
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->string('device_type')->nullable(); // desktop, mobile, tablet
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->integer('duration_seconds')->default(0);
            $table->boolean('is_unique')->default(true);
            $table->timestamps();

            $table->index(['article_id', 'created_at']);
            $table->index(['user_id', 'created_at']);
            $table->index('ip_address');
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_views');
    }
};
