<?php

namespace Tests\Feature;

use App\Models\Shop;
use App\Models\Slider;
use App\Models\Manufacturer;
use App\Models\Inventory;
use App\Models\Product;
use App\Models\Category;
use App\Models\CategorySubGroup;
use App\Models\Blog;
use App\Models\CategoryGroup;

use Tests\TestCase;

class GuestCustomerApiTest extends TestCase
{

    /**
     * Set up the test suite.
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->setServerIp('127.0.0.1');
    }

    /**
     * Set the IP address in the SERVER super global.
     *
     * @param string $ip
     */
    private function setServerIp(string $ip): void
    {
        $_SERVER['REMOTE_ADDR'] = $ip;
        $_SERVER['HTTP_CLIENT_IP'] = $ip;
    }

    /**
     * Test sliders API route.
     *
     * @return void
     * @covers \App\Http\Controllers\HomeController::sliders
     */
    public function test_sliders_api_route()
    {
        $response = $this->getJson('/api/sliders');

        $response->assertStatus(200);

        // Assert that the response contains the expected JSON structure
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'title_color',
                    'sub_title',
                    'image' => [
                        'id',
                        'path',
                        'name',
                        'extension',
                        'order',
                        'featured'
                    ],
                    'sub_title_color',
                    'link',
                    'order'
                ]
            ]
        ]);
    }


    /**
     * Test banners API route.
     *
     * @return void
     */
    
    public function test_banners_api_route()
    {
        $response = $this->getJson('/api/banners');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'title',
                    'description',
                    'image',
                    'link',
                    'link_label',
                    'bg_color',
                    'group_id'
                ]
            ]
        ]);
    }


    /**
     * Test page API route.
     *
     * @return void
     */
    public function test_page_api_route()
    {
        $response = $this->getJson('/api/page/contact-us');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'id',
                'title',
                'slug',
                'content',
                'updated_at',
                'published_at',
            ]
        ]);
    }

    /**
     * Test currencies API route.
     *
     * This test case sends a GET request to /api/currencies and asserts that the response status is 200.
     * Additionally, it asserts that the response contains the expected JSON structure.
     *
     * @covers \App\Http\Controllers\WalletController::getCurrencies
     */
    public function test_currencies_api_route()
    {
        $response = $this->getJson('/api/currencies');
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'data' => [
                [
                    'id',
                    'name',
                    'iso_code',
                    'iso_numeric',
                    'symbol',
                    'disambiguate_symbol',
                    'subunit',
                    'subunit_to_unit',
                    'html_entity',
                    'decimal_mark',
                    'thousands_separator',
                    'smallest_denomination',
                    'symbol_first',
                    'priority'
                ]
            ]
        ]);
    }

    /**
     * Test system config API route.
     *
     * This test case sends a GET request to /api/system_configs and asserts
     * that the response status is 200. Additionally, it asserts that the
     * response contains the expected JSON structure.
     *
     * @return void
     */
    public function test_system_configs_api_route()
    {
        $response = $this->getJson('/api/system_configs');

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data' => [
                'maintenance_mode',
                'install_version',
                'compatible_app_version',
                'name',
                'slogan',
                'legal_name',
                'platform_logo',
                'email',
                'worldwide_business_area',
                'timezone_id',
                'currency_id',
                'default_language',
                'ask_customer_for_email_subscription',
                'can_cancel_order_within',
                'support_phone',
                'support_phone_toll_free',
                'support_email',
                'facebook_link',
                'google_plus_link',
                'twitter_link',
                'pinterest_link',
                'instagram_link',
                'youtube_link',
                'length_unit',
                'weight_unit',
                'valume_unit',
                'decimals',
                'show_currency_symbol',
                'show_space_after_symbol',
                'max_img_size_limit_kb',
                'show_item_conditions',
                'address_default_country',
                'address_default_state',
                'show_address_title',
                'address_show_country',
                'address_show_map',
                'allow_guest_checkout',
                'enable_chat',
                'vendor_get_paid',
                'currency',
                'selected_currency',
                'active_languages',
            ]
        ]);
        
        // Assertions for the currency data
        $currency = $response->json()['data']['currency'];
        $this->assertIsArray($currency);
        $this->assertNotEmpty($currency);

        foreach ($currency as $key => $value) {
            $this->assertIsString($key);
        }

        // Check if the response contains an array of selected currency
        $this->assertArrayHasKey('selected_currency', $response->json()['data']);
        $selectedCurrency = $response->json()['data']['selected_currency'];
        $this->assertNotEmpty($selectedCurrency);
        $this->assertIsArray($selectedCurrency);

        // Check if the response contains an array of active languages
        $activeLanguages = $response->json()['data']['active_languages'];
        $this->assertIsArray($activeLanguages);
        $this->assertNotEmpty($activeLanguages);

        foreach ($activeLanguages as $key => $value) {
            $this->assertIsString($key);
            $this->assertIsString($value);
        }
    }

    public function test_plugin_route()
    {
        $this->getJson('/api/plugin/core')->assertOk();
    }

    public function test_blogs_route()
    {
        $this->getJson('/api/blogs')->assertOk();
    }

    public function test_blog_route()
    {
        $blogSlugs = Blog::pluck('slug')->toArray();

        foreach($blogSlugs as $blogSlug){
            $response = $this->getJson('/api/blog/'.$blogSlug);
            $response->assertOk();
        }
    }

    public function test_featured_categories_route()
    {
        $this->getJson('/api/featured-categories')->assertOk();
    }

    /** @test */
    public function test_trending_categories_route()
    {
        $this->getJson('/api/trending-categories')->assertOk();
    }

    /** @test */
    public function test_category_group_route()
    {
        $this->getJson('/api/category-grps')->assertOk();
    }

    /** @test */
    public function test_category_sub_group_route()
    {
        $this->getJson('/api/category-subgrps')->assertOk();
    }

    /** @test */
    public function test_categories_route()
    {
        $this->getJson('/api/categories')->assertOk();
    }

    /** @test */
    public function test_shops_route()
    {
        $this->getJson('/api/shops')->assertOk();
    }

    public function test_shop_route()
    {
        $shopSlugs = Shop::pluck('slug')->toArray();

        foreach($shopSlugs as $shopSlug){
            $response = $this->getJson('/api/shop/'.$shopSlug);
            $response->assertOk();
        }
    }

    public function test_shop_listings_route()
    {
        $shopSlugs = Shop::pluck('slug')->toArray();

        foreach($shopSlugs as $shopSlug){
            $response = $this->getJson('/api/shop/'.$shopSlug.'/listings');
            $response->assertOk();
        }
    }

    public function test_shop_feedbacks_route()
    {
        $shopSlugs = Shop::pluck('slug')->toArray();

        foreach($shopSlugs as $shopSlug){
            $response = $this->getJson('/api/shop/'.$shopSlug.'/feedbacks');
            $response->assertOk();
        }
    }

    public function test_shop_warehouses_route()
    {
        $shopSlugs = Shop::pluck('slug')->toArray();

        foreach($shopSlugs as $shopSlug){
            $response = $this->getJson('/api/shop/'.$shopSlug.'/warehouses');
            $response->assertOk();
        }
    }

    public function test_brands_route()
    {
        $this->getJson('/api/brands')->assertOk();
    }

    public function test_featured_brands_route()
    {
        $this->getJson('/api/brands/featured')->assertOk();
    }

    /**
     * Test for specific brand route
     */
    public function test_brand_route()
    {
        $brandSlugs = Manufacturer::pluck('slug')->toArray();

        foreach($brandSlugs as $brandSlug){
            $response = $this->getJson('/api/brand/'.$brandSlug);
            $response->assertOk();
        }
    }

    /**
     *  test for specific brand listings route
     */
    public function test_brand_listings_route()
    {
        $brandSlugs = Manufacturer::pluck('slug')->toArray();

        foreach($brandSlugs as $brandSlug){
            $response = $this->getJson("/api/brand/{$brandSlug}/listings");
            $response->assertOk();
        }
    }

    /**
     * Tests ti see for products offer by slug
     * @return void
     */
    public function test_offers_route()
    {
        $productSlugs = Product::inRandomOrder()->take(5)->pluck('slug')->toArray();

        foreach($productSlugs as $productSlug){
            $response = $this->getJson('/api/offers/'.$productSlug);
            $response->assertOk();
        }
    }

    public function test_listings_route()
    {
        $this->getJson('/api/listings/latest')->assertOk();
    }

    public function test_listing_route()
    {
        $inventorySlugs = Inventory::inRandomOrder()->take(5)->pluck('slug')->toArray();

        foreach($inventorySlugs as $inventorySlug){
            $this->getJson('/api/listing/'.$inventorySlug)->assertOk();
        }
    }

    public function test_listing_category_route()
    {
        $categorySlugs = Category::inRandomOrder()->take(5)->pluck('slug')->toArray();

        foreach($categorySlugs as $categorySlug){
            $this->getJson('/api/listing/category/'.$categorySlug)->assertOk();
        }
    }

    public function test_listing_category_sub_group_route()
    {
        $slugs = CategorySubGroup::inRandomOrder()->take(5)->pluck('slug')->toArray();

        foreach($slugs as $categorySubGroupSlug){
            $this->getJson('/api/listing/category-subgrp/'.$categorySubGroupSlug)->assertOk();
        }
    }

    public function test_listing_category_group_route()
    {
        $slugs = CategoryGroup::inRandomOrder()->take(5)->pluck('slug')->toArray();

        foreach($slugs as $categoryGroupSlug){
            $this->getJson('/api/listing/category-grp/'.$categoryGroupSlug)->assertOk();
        }
    }

    public function test_listing_feedbacks_route()
    {
        $inventorySlugs = Inventory::inRandomOrder()->take(5)->pluck('slug')->toArray();

        foreach($inventorySlugs as $inventorySlug){
            $this->getJson('/api/listing/'.$inventorySlug.'/feedbacks')->assertOk();
        }
    }

    public function test_recently_viewed_items_route()
    {
        $this->getJson('/api/recently_viewed_items')->assertOk();
    }

    public function test_flash_deals_route()
    {
        $this->getJson('/api/deals/flash-deals')->assertOk();
    }

    public function test_under_the_price_route()
    {
        $this->getJson('/api/deals/under-the-price')->assertOk();
    }

    public function test_deal_of_the_day_route()
    {
        $this->getJson('/api/deals/deal-of-the-day')->assertOk();
    }

    public function test_tagline_route()
    {
        $this->getJson('/api/deals/tagline')->assertOk();
    }
    
}
