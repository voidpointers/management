<?php

namespace Etsy\Requests;

use GuzzleHttp\Client;
use Product\Entities\Image;
use Product\Entities\Listing;

class ListingRequest
{
    public function pull($shop_id)
    {
        set_time_limit(0);

        $url = env('ETSY_URL') . "/listings/{$shop_id}";

        $client = new Client();

        $page = 1;
        while ($page) {
            $response = $client->request('GET', $url, [
                'auth' => ['user', 'pass'],
                'query' => ['limit' => 25, 'page' => $page]
            ]);
            $body = json_decode($response->getBody()->getContents(), true);

            // 存储到数据库
            $data = $body['results'];
            (new Listing)->store($shop_id, $data);
            (new Image())->store($data);
            foreach ($data as $datum) {
                (new InventoryRequest())->pull($datum['listing_id']);
            }

            echo "当前处理页数: " . $page . PHP_EOL;
            // 最后一页为null，退出循环
            $page = $body['pagination']['next_page'];
            usleep(100);
        }
        return true;
    }
}
