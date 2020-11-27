<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PreProcessSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            ['name' => 'view_module'],
            ['name' => 'update_module'],
            ['name' => 'delete_module'],
            ['name' => 'view_folder'],
            ['name' => 'update_folder'],
            ['name' => 'delete_folder'],
            ['name' => 'view_class'],
            ['name' => 'update_class'],
            ['name' => 'delete_class'],
        ]);
        DB::table('roles')->insert([
            ['name' => 'creator'],
            ['name' => 'viewer']
        ]);
        DB::table('permission_role')->insert([
            ['permission_id' => 1, 'role_id' => 1],
            ['permission_id' => 1, 'role_id' => 2],
            ['permission_id' => 2, 'role_id' => 1],
            ['permission_id' => 3, 'role_id' => 1],
            ['permission_id' => 4, 'role_id' => 1],
            ['permission_id' => 4, 'role_id' => 2],
            ['permission_id' => 5, 'role_id' => 1],
            ['permission_id' => 6, 'role_id' => 1],
            ['permission_id' => 7, 'role_id' => 1],
            ['permission_id' => 7, 'role_id' => 2],
            ['permission_id' => 8, 'role_id' => 1],
            ['permission_id' => 9, 'role_id' => 1],
        ]);
    }
}
