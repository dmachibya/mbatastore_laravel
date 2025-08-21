<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddFcmTokenToCustomersAndDeliveryBoysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('customers', ['fcm_token'])) {
            Schema::table('customers', function (Blueprint $table) {
                $table->string('fcm_token')->nullable()->after('api_token');
            });
        }

        if (!Schema::hasColumns('delivery_boys', ['fcm_token'])) {
            Schema::table('delivery_boys', function (Blueprint $table) {
                $table->string('fcm_token')->nullable()->after('api_token');
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
        if (Schema::hasColumns('customers', ['fcm_token'])) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }

        if (Schema::hasColumns('delivery_boys', ['fcm_token'])) {
            Schema::table('delivery_boys', function (Blueprint $table) {
                $table->dropColumn('fcm_token');
            });
        }
    }
}
