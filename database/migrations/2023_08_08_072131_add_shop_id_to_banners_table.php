<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShopIdToBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('banners', function (Blueprint $table) {
            $table->integer('shop_id')->unsigned()->nullable()->after('columns');
        });

        Schema::table('sliders', function (Blueprint $table) {
            $table->integer('shop_id')->unsigned()->nullable()->after('order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sliders', function (Blueprint $table) {
            $table->dropColumn('shop_id');
        });

        Schema::table('banners', function (Blueprint $table) {
            $table->dropColumn('shop_id');
        });
    }
}
