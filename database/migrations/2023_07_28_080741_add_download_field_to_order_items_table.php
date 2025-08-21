<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDownloadFieldToOrderItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('order_items', ['download'])) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->integer('download')->after('unit_price')->default(0);
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
        if (Schema::hasColumns('order_items', ['download'])) {
            Schema::table('order_items', function (Blueprint $table) {
                $table->dropColumn('download');
            });
        }
    }
}
