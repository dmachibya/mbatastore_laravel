<?php

namespace Database\Seeders;

use App\Helpers\PackageSeeder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FlashdealOptionsSeeder extends PackageSeeder
{
    public function run()
    {
//        $now = Carbon::Now();
//        $table = get_option_table_name();
//        $prefix = 'flashdeal_';

        $listings = DB::table('inventories')->pluck('id')->take(6)->toArray();
        $featured = DB::table('inventories')->pluck('id')->take(2)->toArray();

        $data = [
            'start_time' => Carbon::now(),
            'end_time' => Carbon::now()->addMonth(),
            'listings' => $listings,
            'featured' => $featured
        ];

        DB::table(get_option_table_name())->updateOrInsert(
            ['option_name' => 'flashdeal_items'],
            [
                'option_name' => 'flashdeal_items',
                'option_value' => serialize($data),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]
        );


//        foreach ($options as $option) {
//            $common = [
//                'option_value' => $option['option_value'],
//                'autoload' => $option['autoload'],
//                'created_at' => $now,
//                'updated_at' => $now,
//            ];
//
//            if (DB::table($table)->where('option_name', $option['option_name'])->first()) {
//                if ($option['overwrite']) {
//                    DB::table($table)->where('option_name', $option['option_name'])->update($common);
//                }
//            } else {
//                DB::table($table)->insert(array_merge($common, ['option_name' => $option['option_name']]));
//            }
//        }

        // Seed Permissions
//        $actions = 'manage';
//        $this->seedPermissions('Flash Deal', 'Platform', $actions);
    }
}
