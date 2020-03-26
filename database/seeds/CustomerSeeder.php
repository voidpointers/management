<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('customers')->truncate();
        $data = [
            [
                'user_id' => 125136382,
                'username' => '86zsocy7',
                'avatar' => 'https://www.etsy.com/images/avatars/default_avatar_75x75.png',
                'display_name' => 'Smile Sloth',
                'shop_name' => 'FastestSloth',
                'shop_avatar' => 'https://i.etsystatic.com/isla/4e3291/30905590/isla_fullxfull.30905590_i10ny98g.jpg?version=0',
                'is_seller' => 1,
            ]
        ];
        DB::table('customers')->insert($data);
    }
}
