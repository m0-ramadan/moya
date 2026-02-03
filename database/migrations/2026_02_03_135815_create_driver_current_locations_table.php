<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDriverCurrentLocationsTable extends Migration
{
    public function up()
    {
        Schema::create('driver_current_location', function (Blueprint $table) {
            $table->id();
            
            // الربط بالسواق
            $table->unsignedBigInteger('driver_id')->unique(); // كل سواق له موقع واحد فقط
            $table->foreign('driver_id')->references('id')->on('drivers')->onDelete('cascade');
            
            // الموقع
            $table->decimal('lat', 10, 7); // خط العرض
            $table->decimal('lng', 10, 7); // خط الطول
            $table->decimal('speed', 8, 2)->nullable(); // السرعة بالكيلو/ساعة (اختياري)
            $table->decimal('heading', 5, 2)->nullable(); // اتجاه الحركة (اختياري)
            
            // آخر تحديث
            $table->timestamp('last_updated_at')->nullable();
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('driver_current_location');
    }
}
