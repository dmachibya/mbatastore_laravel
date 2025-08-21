<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSmartFormIdForVendorAdditionalInfoToSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('systems', function (Blueprint $table) {
            if (!Schema::hasColumn('systems', 'smart_form_id_for_vendor_additional_info')) {
                $table->bigInteger('smart_form_id_for_vendor_additional_info')
                    ->nullable()->after('vendor_needs_approval');
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
            if (Schema::hasColumn('systems', 'smart_form_id_for_vendor_additional_info')) {
                $table->dropColumn('smart_form_id_for_vendor_additional_info');
            }
        });
    }
}
