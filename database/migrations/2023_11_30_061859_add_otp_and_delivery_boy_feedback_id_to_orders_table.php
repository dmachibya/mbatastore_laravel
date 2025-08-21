<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddOtpAndDeliveryBoyFeedbackIdToOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumns('orders', ['otp', 'delivery_boy_feedback_id'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->string('otp')->after('tracking_id');
                $table->unsignedBigInteger('delivery_boy_feedback_id')->nullable()->after('feedback_id');
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
        if (Schema::hasColumns('orders', ['otp', 'delivery_boy_feedback_id'])) {
            Schema::table('orders', function (Blueprint $table) {
                $table->dropColumn('delivery_boy_feedback_id');
                $table->dropColumn('otp');
            });
        }
    }
}
