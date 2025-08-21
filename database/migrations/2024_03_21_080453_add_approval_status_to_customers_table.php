<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddApprovalStatusToCustomersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('customers', 'approval_status')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->boolean('approval_status')->nullable()->after('active');
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
        if (Schema::hasColumn('customers', 'approval_status')) {
            Schema::table('customers', function (Blueprint $table) {
                $table->dropColumn('approval_status');
            });
        }
    }
}
