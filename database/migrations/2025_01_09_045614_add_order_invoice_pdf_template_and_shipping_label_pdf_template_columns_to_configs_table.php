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
        Schema::table('configs', function (Blueprint $table) {
            if (!Schema::hasColumn('configs', 'order_invoice_pdf_template')) {
                $table->integer('order_invoice_pdf_template')->nullable();
            }

            if (!Schema::hasColumn('configs', 'shipping_label_pdf_template')) {
                $table->integer('shipping_label_pdf_template')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('configs', function (Blueprint $table) {
            if (Schema::hasColumn('configs', 'order_invoice_pdf_template')) {
                $table->dropColumn('order_invoice_pdf_template');
            }

            if (Schema::hasColumn('configs', 'shipping_label_pdf_template')) {
                $table->dropColumn('shipping_label_pdf_template');
            }
        });
    }
};
