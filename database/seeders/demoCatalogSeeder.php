<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Country;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\Inventory;
use Illuminate\Support\Str;
use App\Models\Manufacturer;
use App\Models\CategoryGroup;
use App\Models\AttributeValue;
use Illuminate\Database\Seeder;
use App\Models\CategorySubGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Artisan;

class demoCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::Now();
        // DB::table('attributes')->delete(); // Temp
        // DB::table('category_sub_groups')->delete();

        $path = base_path('database/seeders/data/catalog/categories');
        $Grps = CategoryGroup::all();
        foreach ($Grps as $Grp) {
            $file = $path . '/' . $Grp->slug . '.json';

            if (!file_exists($file)) {
                fopen($file, 'w');
            }

            $json = json_decode(file_get_contents($file), true);

            if (!$json) continue;  // If the json decode returns null, for invalid json

            $grpID = $Grp->id;

            // DB::table('category_sub_groups')->where('category_group_id', $grpID)->delete();

            foreach ($json as $subG => $tSubcat) {
                $catSubGroup = CategorySubGroup::where('slug', $tSubcat['slug'])->first();
                if (!$catSubGroup) {
                    $catSubGroup = CategorySubGroup::create([
                        'name' => $subG,
                        'category_group_id' => $grpID,
                        'slug' => $tSubcat['slug'],
                        'description' => $tSubcat['description'],
                        'meta_title' => $subG,
                        'meta_description' => $tSubcat['description'],
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }

                foreach ($tSubcat['subcategories'] as $cat => $tcat) {
                    $category = Category::where('slug', $tcat['slug'])->first();
                    if (!$category) {
                        Category::create([
                            'name' => $tcat['name'],
                            'category_sub_group_id' => $catSubGroup->id,
                            'slug' => $tcat['slug'],
                            'description' => $tcat['description'],
                            'meta_title' => $tcat['name'],
                            'meta_description' => $tcat['description'],
                            'created_at' => $now,
                            'updated_at' => $now,
                        ]);
                    }

                    $tjson = base_path('database/seeders/data/catalog/products') . '/' . $tcat['slug'] . '.json';
                    if (!file_exists($tjson)) {
                        fopen($tjson, 'w');
                    }

                    $tjson = base_path('database/seeders/data/catalog/attributes') . '/' . $tcat['slug'] . '.json';
                    if (!file_exists($tjson)) {
                        fopen($tjson, 'w');
                    }
                }
            }

            // Update products
            $tCats = Category::all();
            foreach ($tCats as $tCat) {
                $allAttributes = [];
                $allAttributesValues = [];
                // Attributes
                $file = base_path("database/seeders/data/catalog/attributes/{$tCat->slug}.json");
                if (file_exists($file)) {
                    $json = json_decode(file_get_contents($file), true);
                    if ($json) {
                        // DB::table('attribute_categories')->where('category_id', $tCat->id)->delete();

                        foreach ($json as $k => $tVal) {
                            // Create or update Attribute
                            $attribute = Attribute::where('name', $k)->first();
                            if (!$attribute) {
                                $attribute = Attribute::create([
                                    'name' => $k,
                                    'attribute_type_id' => 2,
                                    'order' => 10,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);
                            }

                            $allAttributes[$attribute->id] = $attribute;

                            foreach ($tVal as $attVal) {
                                $AttributeValue = AttributeValue::where('attribute_id', $attribute->id)
                                    ->where('value', $attVal)->first();
                                if (!$AttributeValue) {
                                    $AttributeValue = AttributeValue::create([
                                        'value' => $attVal,
                                        'attribute_id' => $attribute->id,
                                        'created_at' => $now,
                                        'updated_at' => $now,
                                    ]);
                                }

                                $allAttributesValues[$attribute->id][] = $AttributeValue->id;
                            }

                            DB::table('attribute_categories')->insert([
                                'category_id' => $tCat->id,
                                'attribute_id' => $attribute->id,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ]);
                        }
                    }
                }

                // Process products
                $file = base_path("database/seeders/data/catalog/products/{$tCat->slug}.json");
                if (file_exists($file)) {
                    $jsonProducts = json_decode(file_get_contents($file), true);
                    // if ($tCat->slug == 'guitars') {
                    //     dd($jsonProducts);
                    // } else {
                    //     continue;
                    // }

                    if ($jsonProducts) {
                        foreach ($jsonProducts as $k => $tVal) {
                            // Create or update Manufacturer
                            $manufacturer = Manufacturer::where('name', $tVal['manufacturer'])->first();
                            if (!$manufacturer) {
                                $manufacturer = Manufacturer::create([
                                    'name' => $tVal['manufacturer'],
                                    'slug' => Str::slug($tVal['manufacturer'])
                                ]);
                            }

                            // Create or update Country
                            $country = Country::where('name', $tVal['origin_country'])->first();
                            if (!$country) {
                                $country = country::create([
                                    'name' => $tVal['origin_country'],
                                    'full_name' => $tVal['origin_country'],
                                ]);
                            }

                            $newProduct = Product::where("slug", $tVal['slug'])->first();
                            if (!$newProduct) {
                                $newProduct = Product::create([
                                    "name" => $tVal['name'],
                                    "slug" => $tVal['slug'],
                                    "brand" => $tVal['brand'],
                                    "model_number" => $tVal['model_number'],
                                    "mpn" => $tVal['mpn'],
                                    "gtin" => $tVal['gtin'],
                                    "gtin_type" => $tVal['gtin_type'],
                                    "description" => $tVal['description'],
                                    "origin_country" => $country->id,
                                    "manufacturer_id" => $manufacturer->id,
                                ]);

                                DB::table('category_product')->insert([
                                    'product_id' => $newProduct->id,
                                    'category_id' => $tCat->id,
                                    'created_at' => $now,
                                    'updated_at' => $now,
                                ]);
                            }

                            // Process listings
                            $listingsFile = base_path("database/seeders/data/catalog/stocks/{$newProduct->slug}.json");
                            if (file_exists($listingsFile)) {
                                $jsonListings = json_decode(file_get_contents($listingsFile), true);
                                if ($jsonListings) {
                                    $arr = $arr_history = DB::table('shops')->pluck('id', 'owner_id')->toArray();
                                    foreach ($jsonListings as $listing) {
                                        if (Inventory::where('slug', $listing['slug'])->first()) continue;

                                        // If the history array is empty, re-populate it.
                                        if (empty($arr_history)) {
                                            $arr_history = $arr;
                                        }

                                        // Select a random key.
                                        $key = array_rand($arr_history, 1);

                                        $tCommon = [
                                            'shop_id' => $arr_history[$key],
                                            'user_id' => $key,
                                            'product_id' => $newProduct->id,
                                            'title' => $listing['title'],
                                            "description" => $listing['description'],
                                            'key_features' => $listing['key_features'],
                                            'brand' => $newProduct->brand,
                                            'sale_price' => $listing['price'],
                                            'offer_price' => $listing['offer_price'],
                                            'offer_start' => $listing['offer_price'] ? Carbon::Now()->format('Y-m-d h:i a') : null,
                                            'offer_end' => $listing['offer_price'] ? date('Y-m-d h:i a', strtotime(rand(3, 22) . ' days')) : null,
                                            'shipping_weight' => $listing['shipping_weight'],
                                            'free_shipping' => rand(0, 1),
                                            'stuff_pick' => rand(0, 1),
                                            'stock_quantity' => $listing['stock_quantity'],
                                            'sold_quantity' => rand(1, $listing['stock_quantity']),
                                            'meta_title' => $listing['meta_title'],
                                            'meta_description' => $listing['meta_description'],
                                            'available_from' => $now->subDays(rand(1, 30))->format('Y-m-d h:i a'),
                                        ];

                                        // Remove the key/pair from the array.
                                        unset($arr_history[$key]);

                                        $newListings = [];
                                        $newListings[] = Inventory::create(array_merge([
                                            'slug' => $listing['slug'],
                                            'sku' => $listing['sku']
                                        ], $tCommon));

                                        if (!empty($allAttributes)) {
                                            for ($i = 1; $i < rand(2, 3); $i++) {
                                                $newListings[] = Inventory::create(array_merge([
                                                    'parent_id' => $newListings[0]->id,
                                                    'slug' => $listing['slug'] . '-' . $i,
                                                    'sku' => $listing['sku'] . $i
                                                ], $tCommon));
                                            }

                                            foreach ($newListings as $newListing) {
                                                foreach ($allAttributes as $allAttribute) {
                                                    $valAss = $allAttributesValues[$allAttribute->id];
                                                    DB::table('attribute_inventory')
                                                        ->insert([
                                                            'attribute_id' => $allAttribute->id,
                                                            'attribute_value_id' => $valAss[array_rand($valAss)],
                                                            'inventory_id' => $newListing->id,
                                                            'created_at' => $now,
                                                            'updated_at' => $now,
                                                        ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                fopen($listingsFile, 'w');
                            }
                        }
                    } else {
                        // echo "File content can\'t be decode as json: " . $file . PHP_EOL;
                    }
                }
            }
        }

        Artisan::call('cache:clear');

        return 'Success';
    }
}
