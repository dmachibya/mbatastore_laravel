<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalSoldAmountToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'total_sold_amount')) {
                $table->decimal('total_sold_amount', 64, 6)->default(0)->after('current_sale_amount');
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
            if (Schema::hasColumn('shops', 'total_sold_amount')) {
                $table->dropColumn('total_sold_amount');
            }
        });
    }
}
