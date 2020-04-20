<?php

namespace Etsy\Requests;

use Illuminate\Support\Facades\DB;
use Product\Entities\Image;
use Product\Entities\Inventory;
use Product\Entities\Listing;
use Voidpointers\Etsy\Facades\Etsy;

class ListingRequest
{
    public function pull($params)
    {
        set_time_limit(0);

        $page = 1;
        while ($page) {
            $listings = Etsy::findAllShopListingsActive([
                'params' => $params,
                'associations' => ['Images']
            ]);

            $data = $listings['results'];

            $vars = [];
            foreach ($data as $key => $datum) {
                $listing_id = $datum['listing_id'];
                $temp = Etsy::getInventory([
                    'params' => [
                        'listing_id' => $listing_id
                    ]
                ]);
                $products = $temp['results']['products'];
                foreach ($products as $key => $product) {
                    $product['listing_id'] = $listing_id;
                    $vars[] = $product;
                }
            }

            // 存储到数据库
            (new Listing())->store($data);
            (new Image)->store($data);
            (new Inventory)->store($vars);

            // 最后一页为null，退出循环
            $page = $listings['pagination']['next_page'];
        }
        return true;
    }

    public function renew($listing_id)
    {
        $listing = DB::table('shops')->get();
        foreach ($listing as $user) {
            echo $user->listing_id;
        }exit;
        //测试权限
        $access_token = env('ETSY_ACCESS_TOKEN');
        $access_token_secret = env('ETSY_ACCESS_TOKEN_SECRET');

        $oauth = new OAuth(env('ETSY_CONSUMER_KEY'), env('ETSY_CONSUMER_SECRET'),
            OAUTH_SIG_METHOD_HMACSHA1, OAUTH_AUTH_TYPE_URI);var_dump($oauth);exit;
        $oauth->setToken($access_token, $access_token_secret);

        try {
            $data = $oauth->fetch("https://openapi.etsy.com/v2/oauth/scopes", null, OAUTH_HTTP_METHOD_GET);
            $json = $oauth->getLastResponse();
            print_r(json_decode($json, true));

        } catch (\OAuthException $e) {
            error_log($e->getMessage());
            error_log(print_r($oauth->getLastResponse(), true));
            error_log(print_r($oauth->getLastResponseInfo(), true));
            exit;
        }

//        $a = Etsy::updateListing(
//            ['params' => ['listing_id' => $listing_id, 'renew' => true]]
//        );var_dump($a);exit;
    }
}
