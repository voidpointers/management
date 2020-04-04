<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ShopsSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        DB::table('shops')->truncate();

        DB::table('shops')->insert([
            [
                'id' => 1,
                'shop_id' => 16333181,
                'user_id' => 125136382,
                'shop_name' => 'FastestSloth',
                'username' => '86zsocy7',
                'title' => '',
                'currency_code' => 'USD',
                'shop_name_zh' => '',
                'url' => 'https://www.etsy.com/shop/FastestSloth?utm_source=etsy2csv&utm_medium=api&utm_campaign=api',
                'image' => '',
                'icon' => 'https://i.etsystatic.com/isla/4e3291/30905590/isla_fullxfull.30905590_i10ny98g.jpg?version=0',
                'consumer_key' => 'rcanvluyjr4dkatojohoggge',
                'consumer_secret' => 'd8m65tgf18',
                'access_token' => '76fd62c2c980ecd90b4af0baaf8fee',
                'access_secret' => 'd5cf360a96',
                'status' => 1,
                'ip' => '149.248.7.236',
                'create_time' => 0,
                'update_time' => 1585376553,
            ],

            [
                'id' => 2,
                'shop_id' => 16407439,
                'user_id' => 126311338,
                'shop_name' => 'CrystalMaggie',
                'username' => 'f4hzxkka',
                'title' => '',
                'currency_code' => 'USD',
                'shop_name_zh' => '',
                'url' => 'https://www.etsy.com/shop/CrystalMaggie?utm_source=etsy2csv&utm_medium=api&utm_campaign=api',
                'image' => 'https://i.etsystatic.com/iusb/81be7c/36804078/iusb_760x100.36804078_h0j3.jpg?version=0',
                'icon' => 'https://i.etsystatic.com/isla/05416e/37750567/isla_fullxfull.37750567_5vnn0e6n.jpg?version=0',
                'consumer_key' => 'ue9wa7sm4ff64kyr91ili2i1',
                'consumer_secret' => 'hebb1yy381',
                'access_token' => '63980e0ee32c7eae8067aad0d47585',
                'access_secret' => 'c702f8f38f',
                'status' => 1,
                'ip' => '',
                'create_time' => 0,
                'update_time' => 0,
            ],
        ]);
    }
}
