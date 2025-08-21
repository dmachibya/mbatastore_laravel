<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPickupEnabledToShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('configs', 'pickup_enabled')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->boolean('pickup_enabled')->default(true);
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
        if (Schema::hasColumn('configs', 'pickup_enabled')) {
            Schema::table('configs', function (Blueprint $table) {
                $table->dropColumn('pickup_enabled');
            });
        }
    }
}
