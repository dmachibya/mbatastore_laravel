<?php

use App\Models\Order;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFulfilmentTypeFieldsToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('orders', 'fulfilment_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('fulfilment_type')->default(Order::FULFILMENT_TYPE_DELIVER)->after('shipping_address');
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
        if (Schema::hasColumn('orders', 'fulfilment_type')) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('fulfilment_type');
            });
        }
    }
}
