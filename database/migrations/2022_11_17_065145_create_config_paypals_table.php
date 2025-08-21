<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateConfigPaypalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('config_paypals')) {
            Schema::create('config_paypals', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_id')->unsigned()->index();
                $table->text('client_id')->nullable();
                $table->text('client_secret')->nullable();
                $table->boolean('sandbox')->nullable()->default(true);

                $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
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
        Schema::dropIfExists('config_paypals');
    }
}
