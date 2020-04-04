<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->truncate();
        $data = [
            [
                'username' => 'admin',
                'password' => Hash::make('admin'),
                'create_time' => time(),
                'update_time' => time(),
            ]
        ];
        DB::table('users')->insert($data);
    }
}
