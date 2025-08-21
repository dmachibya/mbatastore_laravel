<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('translation_categories')) {
            Schema::create('translation_categories', function (Blueprint $table) {
                $table->id();
                $table->integer('category_id')->unsigned()->index();
                $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
                $table->string('slug');
                $table->string('lang');
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
        Schema::dropIfExists('translation_categories_table');
    }
}
