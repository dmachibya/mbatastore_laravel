<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ChangeWarehouseIdTypeToString extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('inventories', 'warehouse_id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->string('warehouse_id', 255)->change();
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
        if (Schema::hasColumn('inventories', 'warehouse_id')) {
            Schema::table('inventories', function (Blueprint $table) {
                $table->integer('warehouse_id')->change();
            });
        }
    }
}
