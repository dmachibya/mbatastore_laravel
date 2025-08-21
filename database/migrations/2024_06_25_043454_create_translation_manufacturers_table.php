<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationManufacturersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('translation_manufacturers')) {
            Schema::create('translation_manufacturers', function (Blueprint $table) {
                $table->id();
                $table->unsignedInteger('manufacturer_id')->index();
                $table->foreign('manufacturer_id')->references('id')->on('manufacturers')->onDelete('cascade');
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
        Schema::dropIfExists('translation_manufacturers');
    }
}
