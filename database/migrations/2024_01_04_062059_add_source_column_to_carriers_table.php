<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSourceColumnToCarriersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('carriers', 'source')) {
            Schema::table('carriers', function (Blueprint $table) {
                $table->string('source')->nullable()->after('active');
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
        Schema::table('carriers', function (Blueprint $table) {
            if (Schema::hasColumn('carriers', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
}
