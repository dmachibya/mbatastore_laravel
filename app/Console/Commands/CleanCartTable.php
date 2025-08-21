<?php

namespace App\Console\Commands;

use App\Models\Cart;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class CleanCartTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'incevio:clean-cart-table';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove cart table old data';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        DB::table('carts')
          ->where('created_at', '<', Carbon::now()->subDays(30))
          ->delete();
    }
}
