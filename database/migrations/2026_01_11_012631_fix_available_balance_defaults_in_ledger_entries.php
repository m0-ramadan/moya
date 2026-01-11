<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->decimal('available_balance_before', 15, 2)
                ->default(0)
                ->change();

            $table->decimal('available_balance_after', 15, 2)
                ->default(0)
                ->change();
        });
    }

    public function down()
    {
        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->decimal('available_balance_before', 15, 2)
                ->default(null)
                ->change();

            $table->decimal('available_balance_after', 15, 2)
                ->default(null)
                ->change();
        });
    }
};
