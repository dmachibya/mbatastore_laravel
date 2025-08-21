<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Inventory;

class TranslationsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_translation_is_being_added_for_inventory()
    {
        $response = $this->post('/login',[
            'email' => 'merchant@demo.com',
            'password' => '123456'
        ]);

        $response->assertStatus(302);

        $inventory = Inventory::first();

        $test_data = [
            'title' => 'Translated to Spanish Title',
            'description' => 'Translated to Spanish Description',
            'condition_note' => 'Translated to Spanish condition_note',
            'key_features' => ['Translated key Spanish features 1', 'Translated key Spanish features 2'],
            'lang' => 'es',
        ];
        
        $response = $this->post(route('admin.stock.inventory.translation.store',$inventory),$test_data);
        $response->assertStatus(302); // checking if the store actions is being performed properly

        // Assert that the inventory data exists in the database
        $this->assertDatabaseHas('translation_inventories', [
            'inventory_id' => $inventory->id,
            'lang' => 'es',
            'translation' => serialize([
                'title' => $test_data['title'],
                'description' => $test_data['description'],
                'condition_note' => $test_data['condition_note'],
                'key_features' => $test_data['key_features'],
            ]),
        ]);
    }

    public function test_translation_showing_on_frontend()
    {
        $test_data = [
            'title' => 'Translated to Spanish Title',
            'description' => 'Translated to Spanish Description',
            'condition_note' => 'Translated to Spanish condition_note',
            'key_features' => ['Translated key Spanish features 1', 'Translated key Spanish features 2'],
            'lang' => 'es',
        ];

        // change locale
        $response = $this->get("/locale/{$test_data['lang']}");
        $response->assertStatus(302);

        $inventory = Inventory::first();
        $response = $this->get("/product/{$inventory->slug}");
        
        $response->assertSee([
            $test_data['title'],
            $test_data['description'],
            $test_data['key_features'][0],
            $test_data['key_features'][1],
        ]);
    }
}
