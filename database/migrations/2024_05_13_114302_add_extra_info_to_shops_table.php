<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtraInfoToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('shops', 'extra_info')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->longText('extra_info')->nullable()->after('description');
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
        if (Schema::hasColumn('shops', 'extra_info')) {
            Schema::table('shops', function (Blueprint $table) {
                $table->dropColumn('extra_info');
            });
        }
    }
}
