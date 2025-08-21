<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddShowCustomerTermsAndConditionsColumnToSystemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('systems', function (Blueprint $table) {
            if (!Schema::hasColumn('systems', 'show_customer_terms_and_conditions')) {
                $table->boolean('show_customer_terms_and_conditions')->default(true)->after('ask_customer_for_email_subscription');
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
        if (Schema::hasColumn('systems', 'show_customer_terms_and_conditions')) {
            Schema::table('systems', function (Blueprint $table) {
                $table->dropColumn('show_customer_terms_and_conditions');
            });
        }
    }
}
