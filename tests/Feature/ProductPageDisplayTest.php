<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Inventory;

class ProductPageDisplayTest extends TestCase
{

    public function test_product_can_be_viewed()
    {
        // Make a GET request to the product's view page
        $response = $this->get('/product/abc-xyz-listing-123');

        $response->assertOk();
    }

    public function test_each_active_listing_can_be_viewed()
    {
        $batchSize = 25; // Set the number of listings to process in each iteration
        $listings = Inventory::active()->orderBy('id')->paginate($batchSize);

        foreach ($listings as $listing) {
            $response = $this->get("/product/{$listing->slug}");

            $response->assertOk();
            $response->assertSee($listing->title); // can see the listing title
        }
    }
}
