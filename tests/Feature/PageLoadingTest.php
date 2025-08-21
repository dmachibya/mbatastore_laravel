<?php

namespace Tests\Feature;

use Tests\TestCase;

class PageLoadingTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_homepage_is_being_loaded()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee([
            'Welcome To zCart Marketplace',
            'Featured Categories'
        ]);
    }

    public function test_shops_page_is_being_loaded()
    {
        $response = $this->get('/shops');

        $response->assertStatus(200);
    }

    public function test_auctions_page_is_being_loaded()
    {
        $response = $this->get('/auctions');

        $response->assertStatus(200);
    }

    public function test_events_page_is_being_loaded()
    {
        $response = $this->get('/events');

        $response->assertStatus(200);
    }

    public function test_categories_page_is_being_loaded()
    {
        $response = $this->get('/categories');

        $response->assertStatus(200);
    }

    public function test_brands_page_is_being_loaded()
    {
        $response = $this->get('/brands');

        $response->assertStatus(200);
    }

    public function test_customer_login_page_is_being_loaded()
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_customer_register_page_is_being_loaded()
    {
        $response = $this->get('/customer/register');

        $response->assertStatus(200);
    }

    public function test_customer_forgot_password_page_is_being_loaded()
    {
        $response = $this->get('/customer/password/reset');

        $response->assertStatus(200);
    }
}
