<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('file_chunks', function (Blueprint $table) {
            $table->id();
            $table->string('upload_id', 32)->index();
            $table->integer('chunk_number');
            $table->string('original_name');
            $table->integer('total_chunks');
            $table->bigInteger('total_size');
            $table->bigInteger('file_size')->nullable();
            $table->string('mime_type')->nullable();
            $table->enum('message_type', ['image', 'voice', 'file']);
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('chat_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['upload_id', 'chunk_number', 'user_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('file_chunks');
    }
};
