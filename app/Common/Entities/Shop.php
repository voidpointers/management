<?php

namespace Common\Entities;

use App\Model;

class Shop extends Model
{
    protected $hidden = ['access_token', 'access_secret'];

    protected $table = 'shops';

    public function declare()
    {
        return $this->hasOne(Declares::class, 'shop_id', 'shop_id');
    }

    public function store($params)
    {
        $shop = get_shop($params['shop_id']);

        $data = [
            'shop_name' => $params['shop_name'],
            'user_id' => $params['user_id'],
            'username' => $params['login_name'],
            'title' => $params['title'],
            'currency_code' => $params['currency_code'],
            'url' => $params['url'],
            'image' => $params['image_url_760x100'],
            'icon' => $params['icon_url_fullxfull'],
            'status' => 1,
            'consumer_secret' => $shop['consumer_secret'],
            'consumer_key' => $shop['consumer_key'],
            'access_secret' => $params['access_secret'],
            'access_token' => $params['access_token'],
        ];
        self::updateOrCreate(
            ['shop_id' => $params['shop_id']],
            $data
        );

        return set_shop([$params['shop_id'] => $data]);
    }
}
