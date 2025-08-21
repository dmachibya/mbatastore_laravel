<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupInstructionToWarehousesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('warehouses', 'pickup_instruction')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->text('pickup_instruction')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasColumn('warehouses', 'pickup_instruction')) {
            Schema::table('warehouses', function (Blueprint $table) {
                $table->dropColumn('pickup_instruction');
            });
        }
    }
}
