<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPayToFieldsToShopTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('shops', 'pay_to')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->string('pay_to')->nullable()->after('active');
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
        if (Schema::hasColumn('shops', 'pay_to')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('pay_to');
            });
        }
    }
}
