<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    public function test_customer_dashboard_can_be_viewed()
    {
        $customer = \App\Models\Customer::where('email', 'customer@demo.com')->first();
        $this->post('customer/login',[
            'email' => $customer->email,
            'password' => '123456'
        ]);

        // Act
        $response = $this->get('/my/dashboard');

        // Assert
        $response->assertStatus(200); // returning response
        $this->assertAuthenticatedAs($customer); // auth working
        $response->assertSee([
            'Home',
            'Dashboard',
            $customer->nice_name,
        ]);
    }
}