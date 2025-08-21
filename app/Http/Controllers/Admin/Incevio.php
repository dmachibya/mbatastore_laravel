<?php

namespace App\Http\Controllers\Admin;

use App\Models\Shop;
use App\Models\System;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Artisan;
use App\Jobs\UpdateInventorySoldQuantityJob;

class Incevio extends Controller
{
    public function __construct()
    {
        parent::__construct();

        // Restricted on demo
        if (config('app.demo') == true) {
            echo trans('messages.demo_restriction');
            exit();
        }
    }

    /**
     * Check different type system information
     */
    public function check($option = 'version')
    {
        if ($option == 'geoip' || $option == 'ip') {
            return geoip(get_visitor_IP())->toArray();
        }

        return '<h1 style="margin-top:100px; text-align: center;">Your marketplace running on zCart version: ' . System::VERSION . '</h1>';
    }

    /**
     * New version upgrade
     */
    public function upgrade($option = 'migrate')
    {
        // Flash the composer autoload file
        // Artisan::call('dump-autoload');

        // Upgrading to 2.16
        if (System::VERSION == '2.16.0') {
            // For old orders data insert to sold quantity field of inventory table
            if (Schema::hasColumn('shops', 'total_sold_amount')) {
                Shop::chunk(100, function ($shops) {
                    foreach ($shops as $shop) {
                        $total_sold_amount = $shop->orders->sum('total');

                        if ($shop->total_sold_amount < $total_sold_amount) {
                            $shop->total_sold_amount = $total_sold_amount;
                            $shop->save();
                        }
                    }
                });
            }
        }

        // Upgrading to 2.9
        if (System::VERSION == '2.9.0') {
            //For old orders data insert to sold quantity field of inventory table
            UpdateInventorySoldQuantityJob::dispatch();
        }

        // Upgrading from 2.3 to 2.4
        if (System::VERSION == '2.4.0') {
            $this->zCart24();
        }

        // Upgrading from 2.17 to 2.18
        if (System::VERSION == '2.18.0') {
            $this->zCart218();
        }

        Log::info('Updating version into system table');
        DB::table('systems')->where('id', 1)->update([
            'install_verion' => System::VERSION
        ]);

        // Universal upgrading process
        Artisan::call('migrate');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        Artisan::call('view:clear');
        Artisan::call('incevio:clear-cache');

        return '<info>✔</info> ' . Artisan::output() . '<br/>';
    }

    /**
     * Run Artisan command
     */
    public function command($option = 'job')
    {
        if ($option == 'job') {
            Artisan::call('queue:work');

            return '<info>✔</info> ' . Artisan::output() . '<br/>';
        }

        return 'Invalid command!';
    }

    /**
     * Clear config. cache etc
     */
    public function clear($all = false)
    {
        Artisan::call('optimize:clear');
        $out = '<info>✔</info> ' . Artisan::output() . '<br/><br/>';

        if ($all) {
            Artisan::call('incevio:clear-cache');
            $out .= '<info>✔</info> ' . Artisan::output() . '<br/><br/>';

            Artisan::call('cache:clear');
            $out .= '<info>✔</info> ' . Artisan::output() . '<br/><br/>';
        }

        Artisan::call('incevio:boost');
        $out .= Artisan::output() . '<br/><br/>';

        return $out . '<h3 style="text-align: center;"><a href="' . url()->previous() . '">' . trans('app.back') . '</a></h3>';
    }

    public function tempGetModelName(string $var)
    {
        $arr = explode('\\', $var);

        if (count($arr) == 2) {
            return $arr[0] . '\\Models\\' . $arr[1];
        }

        return null;
    }

    /**
     * Upgrade to zCart 2.4.0 from older version.
     * Updating all morphic relations of the system.
     *
     * @return void
     */
    public function zCart24()
    {
        // Artisan::call('cashier:webhook --disabled');

        $morphs = [
            'addressable_type' => 'addresses',
            'causer_type' => 'activity_log',
            'subject_type' => 'activity_log',
            'attachable_type' => 'attachments',
            'feedbackable_type' => 'avg_feedback',
            'feedbackable_type' => 'feedbacks',
            'imageable_type' => 'images',
            'repliable_type' => 'replies',
            'payable_type' => 'transactions',
            'holder_type' => 'wallets',
            'from_type' => 'transfers',
            'to_type' => 'transfers',
        ];

        foreach ($morphs as $column => $table) {
            // If table not exist
            if (!Schema::hasTable($table)) continue;

            if (Schema::hasColumn($table, $column)) {
                DB::table($table)->select('id', $column)
                    ->chunkById(100, function ($records) use ($table, $column) {
                        Log::info('Updating ' . Str::upper($table) . ' table');

                        foreach ($records as $row) {
                            if (
                                $row->$column &&
                                $model = $this->tempGetModelName($row->$column)
                            ) {
                                DB::table($table)->where('id', $row->id)
                                    ->update([$column => $model]);
                            }
                        }
                    });

                Log::info(Str::upper($table) . ' table updated successfully!');
            }
        }

        // Updating Taggables
        Log::info('Updating Taggables table');

        $rows = DB::table('taggables')->get();
        foreach ($rows as $row) {
            if ($model = $this->tempGetModelName($row->taggable_type)) {
                DB::table('taggables')
                    ->where('tag_id', $row->tag_id)
                    ->where('taggable_id', $row->taggable_id)
                    ->update(['taggable_type' => $model]);
            }
        }

        Log::info('Taggables table updated successfully!');
    }

    function zCart218()
    {
        Schema::dropIfExists('pdf_templates');

        if (!Schema::hasTable('pdf_templates')) {
            DB::table('migrations')->where('migration', '2025_01_01_042623_create_pdf_templates_table')->delete();
        };
    }
}
