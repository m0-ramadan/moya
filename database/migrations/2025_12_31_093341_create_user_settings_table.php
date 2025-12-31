<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('user_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('location_sharing_enabled')->default(false);
            $table->string('language')->default('ar');
            $table->string('theme')->default('light');
            $table->json('saved_addresses')->nullable();
            $table->json('favorite_addresses')->nullable();
            $table->boolean('account_temporarily_disabled')->default(false);
            $table->timestamps();

            $table->unique('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('user_settings');
    }
};
