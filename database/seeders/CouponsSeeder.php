<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Helpers\PackageSeeder;
use Illuminate\Support\Facades\DB;

class CouponsSeeder extends PackageSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Seed Permissions
        $actions = 'view,add,edit,delete';
        $this->seedPermissions('Coupon', 'Merchant', $actions);
    }
}
