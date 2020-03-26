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

        $users = DB::table('customers')
        ->whereIn('user_id', array_column($data, 'user_id'))
        ->get()
        ->pluck('user_id')
        ->all();

        $array = array_filter($data, function ($item) use ($users) {
            if (in_array($item['user_id'], $users)) {
                return false;
            }
            return true;
        });
        DB::table('customers')->insert($array);
    }
}
