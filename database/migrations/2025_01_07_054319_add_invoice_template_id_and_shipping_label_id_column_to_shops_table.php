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
        Schema::table('shops', function (Blueprint $table) {
            if (!Schema::hasColumn('shops', 'order_invoice_template_id')) {
                $table->integer('order_invoice_template_id');
            }

            if (!Schema::hasColumn('shops', 'shipping_label_template_id')) {
                $table->integer('shipping_label_template_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('systems', function (Blueprint $table) {
            if (Schema::hasColumn('shops', 'shipping_label_template_id')) {
                $table->dropColumn('shipping_label_template_id');
            }
            if (Schema::hasColumn('shops', 'order_invoice_template_id')) {
                $table->dropColumn('order_invoice_template_id');
            }
        });
    }
};
