<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('article_shares', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->string('platform'); // facebook, twitter, whatsapp, linkedin, etc.
            $table->string('method'); // direct, social, email, copy_link
            $table->string('ip_address')->nullable();
            $table->json('shared_with')->nullable();
            $table->timestamps();

            $table->index(['article_id', 'platform']);
            $table->index(['user_id', 'created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('article_shares');
    }
};
