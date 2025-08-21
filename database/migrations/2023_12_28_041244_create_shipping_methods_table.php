<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateShippingMethodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('shipping_methods', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code');
            $table->string('name');
            $table->integer('type');
            $table->string('company_name')->nullable();
            $table->text('website')->nullable();
            $table->text('help_doc_link')->nullable();
            $table->text('terms_conditions_link')->nullable();
            $table->text('description')->nullable();
            $table->text('instructions')->nullable();
            $table->text('admin_description')->nullable();
            $table->text('admin_help_doc_link')->nullable();
            $table->boolean('enabled')->default(1);
            $table->integer('order')->default(99);
            $table->timestamps();
        });

        Schema::create('shop_shipping_methods', function (Blueprint $table) {
            $table->integer('shipping_method_id')->unsigned()->index();
            $table->integer('shop_id')->unsigned()->index();
            $table->timestamps();

            $table->foreign('shipping_method_id')->references('id')->on('shipping_methods')->onDelete('cascade');
            $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('shop_shipping_methods');
        Schema::dropIfExists('shipping_methods');
    }
}
