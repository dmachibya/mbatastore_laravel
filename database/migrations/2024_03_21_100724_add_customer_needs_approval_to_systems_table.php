<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomerNeedsApprovalToSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {   // Add checking
        if (!Schema::hasColumn('systems', 'customer_needs_approval')) {
            Schema::table('systems', function (Blueprint $table) {
                $table->boolean('customer_needs_approval')->default(false)->after('ask_customer_for_email_subscription');
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
        if (Schema::hasColumn('systems', 'customer_needs_approval')) {
            Schema::table('systems', function (Blueprint $table) {
                $table->dropColumn('customer_needs_approval');
            });
        }
    }
}
