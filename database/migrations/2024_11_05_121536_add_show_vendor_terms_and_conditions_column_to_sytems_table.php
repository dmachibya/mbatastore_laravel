<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowVendorTermsAndConditionsColumnToSytemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('systems', function (Blueprint $table) {
            if (!Schema::hasColumn('systems', 'show_vendor_terms_and_conditions')) {
                $table->boolean('show_vendor_terms_and_conditions')->default(true);
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
        Schema::table('systems', function (Blueprint $table) {
            if (Schema::hasColumn('systems', 'show_vendor_terms_and_conditions')) {
                $table->dropColumn('show_vendor_terms_and_conditions');
            }
        });
    }
}
