<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationAttributesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('translation_attributes')) {
            Schema::create('translation_attributes', function (Blueprint $table) {
                $table->id();
                $table->integer('attribute_id')->unsigned()->index();
                $table->foreign('attribute_id')->references('id')->on('attributes')->onDelete('cascade');
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
        Schema::dropIfExists('translation_attributes');
    }
}

