<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (! Schema::hasTable('translation_products')) {
            Schema::create('translation_products', function (Blueprint $table) {
                $table->id();
                $table->bigInteger('product_id')->unsigned()->index();
                $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
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
        Schema::dropIfExists('translation_products');
    }
}
