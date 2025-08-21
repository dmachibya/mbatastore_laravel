<?php

namespace App\Helpers;

use Carbon\Carbon;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PackageSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function seedPermissions($module, $access, $actions)
  {
    $now = Carbon::Now();

    // Seed module
    $found = DB::table('modules')->where('name', $module)->first();

    if ($found) {
      $found->update([
        'access' => $access,
        'actions' =>  $actions,
        'updated_at' => $now,
      ]);

      $module_id = $found->id;
    } else {
      $module_id = DB::table('modules')->insertGetId([
        'name' => $module,
        'description' => 'Manage ' . $module,
        'access' => $access,
        'actions' =>  $actions,
        'created_at' => $now,
        'updated_at' => $now,
      ]);
    }

    // Permissions
    $permissions = explode(',', $actions);
    foreach ($permissions as $permission) {
      // Prepare the permission slug
      $slug = strtolower($permission) . '_' . Str::snake($module);

      // Skip if the permission exist
      if (DB::table('permissions')->where('slug', $slug)->first()) continue;

      $permission_id = DB::table('permissions')->insertGetId([
        'module_id' => $module_id,
        'name' => Str::title($permission),
        'slug' => $slug,
        'created_at' => $now,
        'updated_at' => $now,
      ]);

      // Set default admin permission slug access 
      if ($access != 'Merchant') {
        if (!DB::table('permission_role')->where([
          ['permission_id', '=', $permission_id],
          ['role_id', '=', Role::ADMIN],
        ])->first()) {
          DB::table('permission_role')->insert([
            'permission_id' => $permission_id,
            'role_id' => Role::ADMIN,
            'created_at' => $now,
            'updated_at' => $now,
          ]);
        }
      }

      // Set default merchant permission slug access 
      if ($access != 'Platform') {
        if (!DB::table('permission_role')->where([
          ['permission_id', '=', $permission_id],
          ['role_id', '=', Role::MERCHANT],
        ])->first()) {
          DB::table('permission_role')->insert([
            'permission_id' => $permission_id,
            'role_id' => Role::MERCHANT,
            'created_at' => $now,
            'updated_at' => $now,
          ]);
        }
      }
    }
  }
}
