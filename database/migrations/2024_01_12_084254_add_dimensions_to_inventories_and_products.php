<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDimensionsToInventoriesAndProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('inventories', function (Blueprint $table) {
            if (!Schema::hasColumn('inventories', 'length')) {
                $table->decimal('length', 10, 4);
            }
            if (!Schema::hasColumn('inventories', 'width')) {
                $table->decimal('width', 10, 4);
            }
            if (!Schema::hasColumn('inventories', 'height')) {
                $table->decimal('height', 10, 4);
            }
            if (!Schema::hasColumn('inventories', 'distance_unit')) {
                $table->string('distance_unit');
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
        Schema::table('inventories', function (Blueprint $table) {
            if (Schema::hasColumn('inventories', 'length')) {
                $table->dropColumn('length');
            }
            if (Schema::hasColumn('inventories', 'width')) {
                $table->dropColumn('width');
            }
            if (Schema::hasColumn('inventories', 'height')) {
                $table->dropColumn('height');
            }
            if (Schema::hasColumn('inventories', 'distance_unit')) {
                $table->dropColumn('distance_unit');
            }
        });
    }
}
