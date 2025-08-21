<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class VendorAdminPanelPageLoadTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->post('/login',[
            'email' => 'merchant@demo.com',
            'password' => '123456'
        ]);
    }

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_dashboard_page_is_loading_()
    {
        $response = $this->get(route('admin.admin.dashboard'));
        $response->assertOk();

        $response->assertSee([
            'Latest orders',
            'Disputes',
            'History',
            'Top Selling Items'
        ]);
    }

    public function test_catalogue_attributes_page_is_loading()
    {
        $response = $this->get(route('admin.catalog.attribute.index'));

        $response->assertOk();
        $response->assertSee([
            'Order',
            'Name',
            'Type',
            'Option'
        ]);
    }

    public function test_catalogue_product_page_is_loading()
    {
        $response = $this->get(route('admin.catalog.product.index'));

        $response->assertOk();
        $response->assertSee([
            'Image',
            'Name',
            'Type',
            'GTIN',
            'Category',
            'Listing',
            'Option'
        ]);
    }

    public function test_catalogue_manufacturer_page_is_loading()
    {
        $response = $this->get(route('admin.catalog.manufacturer.index'));

        $response->assertOk();
        $response->assertSee([
            'Name',
            'Phone',
            'Email',
            'Country',
            'Products'
        ]);
    }

    public function test_stock_physical_items_page_is_loading()
    {
        $response = $this->get(route('admin.stock.inventory.index','physical'));

        $response->assertOk();
        $response->assertSee([
            'Image',
            'SKU',
            'Title',
            'Condition',
            'Quantity',
            'Option'
        ]);
    }

    public function test_warehouse_page_is_loading()
    {
        $response = $this->get(route('admin.stock.warehouse.index'));
        
        $response->assertOk();
        $response->assertSee([
            'Image',
            'Name',
            'Email',
            'Incharge',
            'Status',
            'Option',
            'Add warehouse',
        ]);
    }

    public function test_supplier_page_is_loading()
    {
        $response = $this->get(route('admin.stock.supplier.index'));

        $response->assertOk();
        $response->assertSee([
            'Name',
            'Email',
            'Status',
            'Option',
            'Add supplier',
            'Contact person',
        ]);
    }
}
