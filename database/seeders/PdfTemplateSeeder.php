<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\PdfTemplate;

class PdfTemplateSeeder extends Seeder
{
    public function run()
    {
        $defaultTemplates = [
            [
                'name' => 'Default Order Invoice',
                'file_name' => 'default_order_invoice',
                'type' => PdfTemplate::TYPE_ORDER_INVOICE,
                'path' => 'pdf_templates/default_order_invoice.blade.php',
                'description' => 'Default template for order invoices.',
                'is_default' => true,
                'active' => true,
                'shop_id' => null
            ],
            [
                'name' => 'Default Shipping Label',
                'file_name' => 'default_shipping_label',
                'path' => 'pdf_templates/default_shipping_label.blade.php',
                'type' => PdfTemplate::TYPE_SHIPPING_LABEL,
                'description' => 'Default template for shipping Label.',
                'is_default' => true,
                'active' => true,
                'shop_id' => null
            ],
            [
                'name' => 'Default Wallet Transaction',
                'file_name' => 'default_wallet_transaction',
                'path' => 'pdf_templates/default_wallet_transaction.blade.php',
                'type' => PdfTemplate::TYPE_WALLET_TRANSACTION,
                'description' => 'Default template for wallet transactions.',
                'is_default' => true,
                'active' => true,
                'shop_id' => null
            ],
            [
                'name' => 'Default Affiliate Wallet Transaction',
                'file_name' => 'default_affiliate_transaction',
                'path' => 'pdf_templates/default_affiliate_wallet_transaction.blade.php',
                'type' => PdfTemplate::TYPE_AFFILIATE_WALLET_TRANSACTION,
                'description' => 'Default template for affiliate\'s wallet transactions.',
                'is_default' => true,
                'active' => true,
                'shop_id' => null
            ],
            [
                'name' => 'Colorful Order Invoice',
                'file_name' => 'colorful_order_invoice',
                'path' => 'pdf_templates/colorful_order_invoice.blade.php',
                'type' => PdfTemplate::TYPE_ORDER_INVOICE,
                'description' => 'A template for order invoice with more color and style.',
                'is_default' => false,
                'active' => true,
                'shop_id' => null
            ],
            [
                'name' => 'Colorful Shipping Label',
                'file_name' => 'colorful_shipping_label',
                'path' => 'pdf_templates/colorful_shipping_label.blade.php',
                'type' => PdfTemplate::TYPE_SHIPPING_LABEL,
                'description' => 'A template for shipping label with more color and style.',
                'is_default' => false,
                'active' => true,
                'shop_id' => null
            ]
        ];

        foreach ($defaultTemplates as $template) {
            PdfTemplate::updateOrCreate(
                ['name' => $template['name']], // Check for existing record by name
                $template // Update or insert data
            );
        }
    }
}
