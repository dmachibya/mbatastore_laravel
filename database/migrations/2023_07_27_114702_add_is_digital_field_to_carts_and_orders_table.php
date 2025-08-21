<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddIsDigitalFieldToCartsAndOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('carts', ['is_digital'])) {
            Schema::table('carts', function (Blueprint $table) {
                $table->boolean('is_digital')->default(0)->after('packaging_id');
            });
        }

        if (!Schema::hasColumns('orders', ['is_digital'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->boolean('is_digital')->default(0)->after('packaging_id');
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
        if (Schema::hasColumns('carts', ['is_digital'])) {
            Schema::table('carts', function (Blueprint $table) {
                $table->dropColumn('is_digital');
            });
        }

        if (Schema::hasColumns('orders', ['is_digital'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('is_digital');
            });
        }
    }
}
