<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('translation_inventories')) {
            Schema::create('translation_inventories', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('inventory_id')->unsigned()->index();
                $table->foreign('inventory_id')->references('id')->on('inventories')->onDelete('cascade');
                $table->string('lang');
                $table->longText('translation')->nullable();
                $table->timestamps();
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
        Schema::dropIfExists('translation_inventories');
    }
}
