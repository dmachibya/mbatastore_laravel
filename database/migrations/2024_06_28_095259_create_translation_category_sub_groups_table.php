<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationCategorySubGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('translation_category_sub_groups')) {
            Schema::create('translation_category_sub_groups', function (Blueprint $table) {
                $table->id();
                $table->integer('category_sub_group_id')->unsigned();
                $table->foreign('category_sub_group_id')->references('id')->on('category_sub_groups')->onDelete('cascade');
                $table->string('lang');
                $table->text('translation');
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
        Schema::dropIfExists('translation_category_sub_groups');
    }
}
