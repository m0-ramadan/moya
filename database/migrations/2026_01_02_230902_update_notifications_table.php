<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateNotificationsTable extends Migration
{
    public function up()
    {
        Schema::table('notifications', function (Blueprint $table) {
            // Add index for better performance
            $table->index(['notifiable_type', 'notifiable_id', 'is_read']);
            $table->index(['type', 'created_at']);
        });
    }

    public function down()
    {
        Schema::table('notifications', function (Blueprint $table) {
            $table->dropIndex(['notifiable_type', 'notifiable_id', 'is_read']);
            $table->dropIndex(['type', 'created_at']);
        });
    }
}
