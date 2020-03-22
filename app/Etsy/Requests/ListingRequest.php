<?php

namespace Etsy\Requests;

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

            $inventory = [];
            foreach ($data as $key => $datum) {
                $temp = Etsy::getInventory([
                    'params' => [
                        'listing_id' => $datum['listing_id']
                    ]
                ]);
                $inventory[$key] = $temp;
            }
            dd($inventory);

            // 存储到数据库
            (new Listing)->store($data);
            (new Image())->store($data);
            (new Inventory)->store($inventory);

            echo "当前处理页数: " . $page . PHP_EOL;
            // 最后一页为null，退出循环
            $page = $listings['pagination']['next_page'];
            usleep(100);
        }
        return true;
    }
}
