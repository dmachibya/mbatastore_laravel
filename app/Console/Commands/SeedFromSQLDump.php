<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Order;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Incevio\Package\Eventy\Models\Event;
use Incevio\Package\Announcement\Models\Announcement;

class SeedFromSQLDump extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incevio:seed-sql';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed DEMO content from SQL.dump file.';

    protected $db;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->db = env('DB_DATABASE');
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->call('config:clear');

        $this->call('cache:clear');

        $this->call('incevio:clear-storage');

        // turn off referential integrity
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');

        $this->drop_tables();

        if (config('app.active_theme') == 'nova') {
            $file = database_path('seeders/data/sql/nova.sql');
        } else {
            $file = database_path('seeders/data/sql/zcart.sql');
        }

        // Seed data
        if (file_exists($file)) {
            $this->comment(PHP_EOL . "Seeding data. Please wait, it may take a while...");

            $link = getMysqliConnection();

            $queries = file_get_contents($file);

            mysqli_multi_query($link, $queries) or die(mysqli_error($link));

            do {
                // DON'T REMOVE THIS EMPTY LOOP!
                // This loop is important to run the query synchronously.
            } while (mysqli_next_result($link));

            // Update Expirable Dates of models
            $this->updateExpirableDates();

            // Update Expirable Dates of models
            $this->updateOtherData();

            // Refresh scout indexes
            // $this->call('incevio:fresh-index');

            $this->comment(PHP_EOL . "Seeding completed");
        }

        //turn referential integrity back on
        DB::statement('SET FOREIGN_KEY_CHECKS = 1');

        // Reset demo images
        if (config('app.active_theme') == 'nova') {
            $demo_imgs = public_path('images/demo/imgs-for-nova-sql-import');
        } else {
            $demo_imgs = public_path('images/demo/imgs-for-sql-import');
        }

        if (file_exists($demo_imgs)) {
            $destination = storage_path('app/public/images');

            // Remove old imagaes
            if (file_exists($destination)) {
                File::deleteDirectory($destination);
            }

            // Copy the fresh images
            File::copyDirectory($demo_imgs, $destination);
        }

        return 0;
    }

    /**
     * This method will drop all tables from database
     *
     * @return true
     */
    public function drop_tables()
    {
        $colname = 'Tables_in_' . $this->db;

        $tables = DB::select('SHOW TABLES');

        $droplist = [];
        foreach ($tables as $table) {
            if (isset($table->$colname)) {
                $droplist[] = $table->$colname;
            }
        }

        $droplist = implode(',', $droplist);

        if ($droplist) {
            $this->comment(PHP_EOL . "Dropping all tables. Please wait, it may take a while...");
            // DB::beginTransaction();
            DB::statement("DROP TABLE $droplist");
            // DB::commit();

            $this->comment(PHP_EOL . "All tables were dropped");
        }

        return true;
    }

    /**
     * Update expirable dates of models that need
     *
     * @return void
     */
    private function updateExpirableDates()
    {
        // Update subscription trail dates
        DB::table('subscriptions')->update([
            'trial_ends_at' => Carbon::now()->addDays(rand(10, 30))->format('Y-m-d h:i:a')
        ]);

        // Update inventories
        if (Schema::hasTable('inventories')) {
            // Recently added items
            DB::table('inventories')->inRandomOrder()->limit(10)->update([
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update offer ending time
            DB::table('inventories')->whereNotNull('offer_price')
                ->chunkById(100, function ($inventories) {
                    foreach ($inventories as $inventory) {
                        DB::table('inventories')
                            ->where('id', $inventory->id)
                            ->update([
                                'offer_end' => Carbon::now()->addDays(rand(1, 5))->format('Y-m-d h:i:a')
                            ]);
                    }
                });

            // Update auction items
            if (Schema::hasColumns('inventories', ['auctionable', 'base_price', 'auction_status', 'auction_end'])) {
                DB::table('inventories')->where('auctionable', 1)
                    ->chunkById(100, function ($inventories) {
                        foreach ($inventories as $inventory) {
                            $inventory_auction_data = [
                                'base_price' => $inventory->sale_price,
                                'auction_end' => Carbon::now()->addDays(rand(1, 15))->format('Y-m-d h:i:a'),
                            ];

                            // Ensuring AuctionStatusEnum is not called when it does not exist (when auction package is not installed/downloaded)
                            if (class_exists(\Incevio\Package\Auction\Enums\AuctionStatusEnum::class)) {
                                $inventory_auction_data['auction_status'] = \Incevio\Package\Auction\Enums\AuctionStatusEnum::RUNNING;
                                $inventory_auction_data['bid_accept_action'] = \Incevio\Package\Auction\Enums\AuctionEnum::AUCTION_CONTINUE;
                            }

                            DB::table('inventories')
                                ->where('id', $inventory->id)
                                ->update($inventory_auction_data);
                        }
                    });
            }
        }

        // Update announcements
        if (Schema::hasTable('announcements')) {
            $announcements = Announcement::select('id')->get();

            foreach ($announcements as $announcement) {
                $announcement->expire_at = Carbon::now()->addDays(rand(1, 5))->format('Y-m-d h:i a');
                $announcement->save();
            }
        }

        // Update events
        if (Schema::hasTable('events')) {
            $events = Event::select('id')->get();

            foreach ($events as $event) {
                $event->ends = Carbon::now()->addDays(rand(1, 5))->format('Y-m-d h:i a');
                $event->save();
            }
        }

        // Update orders
        $orders = Order::select('id', 'created_at')->get();

        foreach ($orders as $order) {
            $order->created_at = Carbon::now()->subDays(rand(2, 60))->format('Y-m-d h:i a');
            $order->save();
        }

        // Update flash deals
        if ($option = DB::table(get_option_table_name())->where('option_name', 'flashdeal_items')->first()) {
            $deal_end_time = Carbon::now()->addDays(rand(2, 7));

            // Unserialize the data
            $data = unserialize($option->option_value);

            // Update the desired field
            $data['end_time'] = $deal_end_time;

            // Update the record in the database
            DB::table(get_option_table_name())
                ->where('option_name', 'flashdeal_items')
                ->update([
                    'option_value' => serialize($data), // Serialize the data again
                    'updated_at' => now(),
                ]);

            // Update the flash items offer price and offer ends date
            $items = array_merge($data['listings'], $data['featured']);

            DB::table('inventories')->whereIn('id', $items)->lazyById()
                ->each(function ($temp) use ($deal_end_time) {
                    DB::table('inventories')
                        ->where('id', $temp->id)
                        ->update([
                            'offer_price' => $temp->sale_price - rand(($temp->sale_price / 4), ($temp->sale_price / 2)),
                            'offer_start' => Carbon::now()->format('Y-m-d h:i:a'),
                            'offer_end' => $deal_end_time->format('Y-m-d h:i:a'),
                        ]);
                });
        }
    }

    private function updateOtherData()
    {
        // Update default_affiliate_commission_percentage
        // foreach (DB::table('configs')->get() as $config) {
        //     DB::table('configs')
        //         ->where('shop_id', $config->shop_id)
        //         ->update([
        //             'default_affiliate_commission_percentage' => rand(9, 30)
        //         ]);
        // }
    }
}
