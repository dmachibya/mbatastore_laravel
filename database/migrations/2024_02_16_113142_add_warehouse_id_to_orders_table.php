<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWarehouseIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'warehouse_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('warehouse_id')->nullable()->after('shipping_address');
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
        if (Schema::hasColumn('orders', 'warehouse_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('warehouse_id');
            });
        }
    }
}
