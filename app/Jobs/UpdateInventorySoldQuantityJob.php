<?php

namespace App\Jobs;

use App\Models\Shop;
use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Schema;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdateInventorySoldQuantityJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        // For old orders data insert to sold quantity field of inventory table
        if (Schema::hasColumn('inventories', 'sold_quantity')) {
            Inventory::chunk(100, function ($inventories) {
                foreach ($inventories as $inventory) {
                    $sold_quantity = $inventory->orders->sum('quantity');

                    if ($inventory->sold_quantity < $sold_quantity) {
                        $inventory->sold_quantity = $sold_quantity;
                        $inventory->save();
                    }
                }
            });
        }

        if (Schema::hasColumn('shops', 'total_item_sold')) {
            Shop::chunk(100, function ($shops) {
                foreach ($shops as $shop) {
                    $total_item_sold = $shop->inventories->sum('sold_quantity');

                    if ($shop->total_item_sold < $total_item_sold) {
                        $shop->total_item_sold = $total_item_sold;
                        $shop->save();
                    }
                }
            });
        }
    }
}
