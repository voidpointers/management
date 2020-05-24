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

	/**
	 * @param $params
	 * 按页拉取店铺的商品
	 * 不更新修改中的产品
	 */
	public function pullByPage($params){

		$page_limit = 25;

		$listing = new Listing();

		$params['limit'] = $page_limit;

		if($params['page'] == 10){
			$params['page'] = -1;
		}
		$listings = Etsy::findAllShopListingsActive([
			'params' => $params,
			'associations' => ['Images']
		]);

		$data = $listings['results'];

		//拿到本地的商品和状态
		$listing_states = $listing->whereIn('listing_id', array_column($data, 'listing_id'))
			->pluck('state', 'listing_id' )
			->all();
		$vars = [];
		foreach ($data as $key => $datum) {
			$listing_id = $datum['listing_id'];
			//修改中的跳过 eidt状态
			if (array_key_exists($listing_id, $listing_states) && $listing_states[$listing_id] == 'edit') {
				unset($data[$key]);
				continue;
			}
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
		$listing->store($data);
		(new Image)->storeV2($data);
		(new Inventory)->store($vars);

//		 最后一页为null，退出循环
		$page = $listings['pagination']['next_page'];
		$all_page  = ceil($listings['count'] / $page_limit);
		$return_data = [
			'next_page'=> $page == null ? 0 : $page,
			'all_page' => $all_page
		];
		return $return_data;
	}

	public function getDetailById($listing_id){
		$listing = new Listing();
		$data = $listing->where('listing_id', $listing_id)->with(['images', 'inventories'])->get()->first();
		return $data;
	}


    public function renew($listing_id)
    {
        $listing = DB::table('shops')->get();
        foreach ($listing as $user) {
            echo $user->shop_id;
        }exit;

//        $a = Etsy::updateListing(
//            ['params' => ['listing_id' => $listing_id, 'renew' => true]]
//        );var_dump($a);exit;
    }
}
