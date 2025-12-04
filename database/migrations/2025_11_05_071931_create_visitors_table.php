<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


class CreateVisitorsTable extends Migration
{
    public function up()
    {
        Schema::create('visitors', function (Blueprint $table) {
            $table->bigIncrements('id');

            // أساسي
            $table->string('ip', 45)->nullable()->index(); // IPv6 safe
            $table->string('host')->nullable();

            // request basics
            $table->string('method', 10)->nullable();
            $table->string('path')->nullable();
            $table->string('full_url', 2048)->nullable();
            $table->string('referer', 2048)->nullable();

            // user agent raw + parsed
            $table->text('user_agent')->nullable();
            $table->string('browser')->nullable();
            $table->string('browser_version')->nullable();
            $table->string('platform')->nullable(); // OS
            $table->string('platform_version')->nullable();
            $table->string('device')->nullable(); // phone, tablet, desktop, bot, ...
            $table->boolean('is_mobile')->nullable();
            $table->boolean('is_tablet')->nullable();
            $table->boolean('is_desktop')->nullable();
            $table->boolean('is_bot')->nullable();

            // geo info (nullable if cannot resolve)
            $table->string('country')->nullable()->index();
            $table->string('region')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('timezone')->nullable();

            // extra/technical
            $table->json('headers')->nullable();
            $table->json('query')->nullable(); // GET params if you want (consider privacy)
            $table->json('body')->nullable(); // optional: be careful بحفظ حساسية البيانات
            $table->string('session_id')->nullable()->index();

            // أي حقول إضافية
            $table->string('country_iso')->nullable();

            $table->timestamps();

            // فهارس للـ queries الشائعة
            $table->index(['created_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('visitors');
    }
}  