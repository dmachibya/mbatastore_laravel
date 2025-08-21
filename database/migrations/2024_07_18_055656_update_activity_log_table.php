<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateActivityLogTable extends Migration
{
    public $connection;
    public $table;

    public function __construct()
    {
        $this->connection = config('activitylog.database_connection');
        $this->table = config('activitylog.table_name');
    }

    public function up()
    {
        Schema::connection($this->connection)->table($this->table, function (Blueprint $table) {
            if (!Schema::hasColumn($this->table, 'event')) {
                $table->string('event')->nullable()->after('subject_type');
            }
            if (!Schema::hasColumn($this->table, 'batch_uuid')) {
                $table->uuid('batch_uuid')->nullable()->after('properties');
            }
        });
    }

    public function down()
    {
        Schema::connection($this->connection)->table($this->table, function (Blueprint $table) {
            if (Schema::hasColumn($this->table, 'batch_uuid')) {
                $table->dropColumn('batch_uuid');
            }

            if (Schema::hasColumn($this->table, 'event')) {
                $table->dropColumn('event');
            }
        });
    }
}
