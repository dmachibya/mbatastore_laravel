<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;

class GuestCheckoutTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_direct_checkout()
    {
        $cart = Cart::latest()->first();

        $this->get("cart/{$cart->id}/checkout");

        $response = $this->post(route('order.create',$cart->id));

        $response->assertStatus(403);
    }
}
