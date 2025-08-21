<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCurrencyIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'currency_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->unsignedInteger('currency_id')->nullable()
                    ->default(config('system_settings.currency_id'))->after('quantity');

                $table->foreign('currency_id')->references('id')->on('currencies')->onDelete('set null');
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
        if (Schema::hasColumn('orders', 'currency_id')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropForeign(['currency_id']);
                $table->dropColumn('currency_id');
            });
        }
    }
}
