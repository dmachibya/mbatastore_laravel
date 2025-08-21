<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCatalogSystemEnableToSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('systems', 'catalog_system_enable')) {
            Schema::table('systems', function (Blueprint $table) {
                $table->boolean('catalog_system_enable')->nullable()
                    ->default(true)->after('can_use_own_catalog_only');
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
        if (Schema::hasColumn('systems', 'catalog_system_enable')) {
            Schema::table('systems', function (Blueprint $table) {
                $table->dropColumn('catalog_system_enable');
            });
        }
    }
}
