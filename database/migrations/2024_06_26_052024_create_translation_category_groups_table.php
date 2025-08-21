<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslationCategoryGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('translation_category_groups')) {
            Schema::create('translation_category_groups', function (Blueprint $table) {
                $table->id();
                
                $table->unsignedInteger('category_group_id');
                $table->foreign('category_group_id')->references('id')->on('category_groups')->onDelete('cascade');
                
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
        Schema::dropIfExists('translation_category_groups');
    }
}