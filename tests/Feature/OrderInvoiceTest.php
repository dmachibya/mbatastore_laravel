<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Order;

class OrderInvoiceTest extends TestCase
{
    public function test_invoice_route_returns_pdf_as_superAdmin()
    {
        // Login as super admin
        $this->actingAs(\App\Models\User::where('email', 'superadmin@demo.com')->first());

        $orders = Order::all()->pluck('id');

        foreach ($orders as $orderId) {
            // Make a GET request to the invoice route
            $response = $this->get("/admin/order/order/{$orderId}/invoice");

            // Assert that the response returns no error
            try {
                $response->assertOk();
            } catch (\Exception $e) {
                logger()->info("Failed for order: " . $orderId);
                throw $e;
            }

            // Assert that the response is a PDF file
            $response->assertHeader('Content-Type', 'application/pdf');
        }
    }

    public function test_invoice_route_returns_pdf_as_vendor()
    {
        $shop_owner = \App\Models\User::where('email', 'merchant@demo.com')->first();

        // Login as shop owner
        $this->actingAs($shop_owner);

        $shop = \App\Models\Shop::where('owner_id', $shop_owner->id)->first();

        $orders = $shop->orders()->pluck('id');

        foreach ($orders as $orderId) {
            // Make a GET request to the invoice route
            $response = $this->get("/admin/order/order/{$orderId}/invoice");

            // Assert that the response returns no error
            try {
                $response->assertOk();
            } catch (\Exception $e) {
                logger()->info("Failed for order: " . $orderId);
                throw $e;
            }

            // Assert that the response is a PDF file
            $response->assertHeader('Content-Type', 'application/pdf');
        }
    }

    public function test_invoice_route_returns_pdf_as_customer()
    {
        $customer = \App\Models\Customer::where('email', 'customer@demo.com')->first();

        // Login as shop owner
        $this->actingAs($customer);

        $orders = $customer->orders()->pluck('id');

        foreach ($orders as $orderId) {
            // Make a GET request to the invoice route
            $response = $this->get("/order/invoice/{$orderId}");
            $response->assertStatus(302);
        }
    }
}
