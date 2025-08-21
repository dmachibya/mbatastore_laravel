<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Database\Seeders\PdfTemplateSeeder;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('pdf_templates')) {
            Schema::create('pdf_templates', function (Blueprint $table) {
                $table->id();
                $table->integer('shop_id')->unsigned()->nullable();
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->string('type')->nullable();
                $table->boolean('is_default')->default(false);
                $table->boolean('active')->default(true);
                $table->string('file_name')->nullable();
                $table->string('path')->nullable();
                $table->timestamps();

                $table->foreign('shop_id')->references('id')->on('shops')->onDelete('cascade');
            });

            // Run seeder to add default and system provided templates
            $default_templates_seeder = new PdfTemplateSeeder();
            $default_templates_seeder->run();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pdf_templates');
    }
};
