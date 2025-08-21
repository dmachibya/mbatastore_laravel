<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            if (!Schema::hasColumn('systems', 'show_empty_homepage_slider')) {
                $table->boolean('show_empty_homepage_slider')->default(0);
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            if (Schema::hasColumn('systems', 'show_empty_homepage_slider')) {
                $table->dropColumn('show_empty_homepage_slider');
            }
        });
    }
};
