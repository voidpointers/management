<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomersSeeder extends Seeder
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
                'is_admin' => 0
            ],
            [
                'user_id' => 126311338,
                'username' => 'f4hzxkka',
                'avatar' => 'https://i.etsystatic.com/iusa/d0cdad/60321057/iusa_75x75.60321057_jror.jpg?version=0',
                'display_name' => 'Lakeyla',
                'shop_name' => 'CrystalMaggie',
                'shop_avatar' => 'https://i.etsystatic.com/isla/05416e/37750567/isla_fullxfull.37750567_5vnn0e6n.jpg?version=0',
                'is_seller' => 1,
                'is_admin' => 0
            ],
            [
                'user_id' => 71212610,
                'username' => 'ticomore',
                'avatar' => 'https://i.etsystatic.com/iusa/46a84d/59217540/iusa_75x75.59217540_l8gb.jpg?version=0',
                'display_name' => 'TICOMO',
                'shop_name' => 'ticomo',
                'shop_avatar' => 'https://i.etsystatic.com/isla/c44b89/30933254/isla_fullxfull.30933254_cvbjxilh.jpg?version=0',
                'is_seller' => 1,
                'is_admin' => 0
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

        if ($array) {
            DB::table('customers')->insert($array);
        }
    }
}
