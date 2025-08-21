<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalItemSoldToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'total_item_sold')) {
                $table->integer('total_item_sold')->default(0)->after('active');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shops', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'total_item_sold')) {
                $table->dropColumn('total_item_sold');
            }
        });
    }
}
