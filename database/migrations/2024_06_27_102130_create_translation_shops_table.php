<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationShopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('translation_shops')) {
            Schema::create('translation_shops', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_id')->unsigned()->index();
                $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
                $table->string('lang');
                $table->string('slug');
                $table->longText('translation');
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
        Schema::dropIfExists('translation_shops');
    }
}
