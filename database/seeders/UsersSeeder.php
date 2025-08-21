<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class UsersSeeder extends BaseSeeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'id' => 1,
            'shop_id' => null,
            'role_id' => \App\Models\Role::SUPER_ADMIN,
            'nice_name' => 'SuperAdmin',
            'name' => 'Super Admin',
            'email' => 'superadmin@demo.com',
            'password' => bcrypt('123456'),
            'active' => 1,
            'created_at' => Carbon::Now(),
            'updated_at' => Carbon::Now(),
        ]);

        DB::table('dashboard_configs')->insert([
            'user_id' => 1,
            'created_at' => Carbon::Now(),
            'updated_at' => Carbon::Now(),
        ]);

        $country_id = DB::table('countries')->inRandomOrder()->first()->id;
        $state = DB::table('states')->where('country_id', $country_id)->first();

        DB::table('addresses')->insert([
            'address_type' => 'Primary',
            'addressable_type' => \App\Models\User::class,
            'addressable_id' => 1,
            'address_title' => 'Primary Address',
            'state_id' => $state ? $state->id : null,
            'country_id' => $country_id,
            'created_at' => Carbon::Now(),
            'updated_at' => Carbon::Now(),
        ]);
    }
}
