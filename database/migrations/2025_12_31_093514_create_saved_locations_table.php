<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('saved_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->string('type')->nullable(); // home, work, favorite, etc.
            $table->boolean('is_favorite')->default(false);
            $table->text('additional_info')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'type']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('saved_locations');
    }
};
