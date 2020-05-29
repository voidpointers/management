<?php

namespace Etsy\Requests;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
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
			foreach ($products as $key1 => $product) {
				$product['listing_id'] = $listing_id;
				$vars[] = $product;
			}
			$data[$key]["price_on_property"] = json_encode($temp['results']["price_on_property"]);
			$data[$key]["quantity_on_property"] = json_encode($temp['results']["quantity_on_property"]);
			$data[$key]["sku_on_property"] = json_encode($temp['results']["sku_on_property"]);
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

	public function syncToEtsyAndUpdateLocal($listing){
		$shop_id = $listing["shop_id"];
		$listing_id = $listing["listing_id"];
		Log::info("sdf", [$listing]);
		Cache::store('array')->put('shop_id', $shop_id);//店铺id
		//更新基本信息
		$base_data =  [
			"params" => [
				"listing_id" =>$listing_id
			],
			"data" => [
				"title" => $listing['title_new'],
				"description" => $listing['description_new'],
				"tags" => $listing['tags_new'],
				"state" => $listing["state"],
				"taxonomy_id" => $listing["taxonomy_id_new"][count($listing["taxonomy_id_new"])-1]
			]
		];
		Log::info("listing", $base_data);
		$temp = Etsy::updateListing($base_data);

		//更新图片
		$image_new = $listing["images_new"];
		$images = $listing["images"];//原有的图片
		//设置sort
		$i = 1;
		foreach($image_new as &$image){
			$image["sort"] = $i;
			$i++;
		}
		unset($image);

		//1、有image_id 则需先在平台上delete，然后重新关联
		//2、没有image_id 则需拿到图片文件上传到平台
		foreach($images as $image){//删除关联
			if(isset($image["image_id"])){
				self::deleteListingImage($listing_id, $image["image_id"]);
			}
		}

		foreach($image_new as $image){//重新关联或上传
			if(isset($image["image_id"])){
				self::reAssociateListingImage($listing_id, $image["image_id"], $image["sort"]);
			}else{
				$image_path = str_replace(getenv("API_DOMAIN"), "", $image["url"]);
				$image_path = storage_path("/app/public".$image_path);
				self::uploadListingImage($listing_id, $image_path, $image["sort"]);
			}
		}

		//更新库存：价格，数量，sku
		$inventories = $listing['inventories'];
		$to_etsy_inv = [];
		foreach($inventories as $inventory){
			foreach ($inventory["properties"] as &$properties) {
				$properties["values"] = [$properties["values"]];
			}
			$to_etsy_inv[]  = [
				"sku" => $inventory["sku_new"],
				"property_values" => $inventory["properties"],
				"offerings" => [
					[
						"price" => [
							"currency_formatted_raw" => $inventory["price_new"]
						],
						"quantity" => $inventory["quantity_new"],
						"is_enabled" => $inventory["is_enabled"],
					]
				]
			];
		}
		unset($properties);

		$inventory_data = [
			"params" => [
				"listing_id" =>$listing_id
			],
			"data" => [
				"products" =>[
					"json" => json_encode($to_etsy_inv)
				],
				"price_on_property" => json_decode($listing["price_on_property"]), //控制价格是否独立
				"quantity_on_property" => json_decode($listing["quantity_on_property"]), //控制数量是否独立
				"sku_on_property" => json_decode($listing["sku_on_property"])//控制sku是否独立
			]
		];
		Log::info("toEtsyInv", $inventory_data);
		$inv_return = Etsy::updateInventory($inventory_data);


		//从etsy拉取数据保存到本地
		$return_listing_data = Etsy::getListing([
			'params' => [
				'listing_id' => $listing_id,
			],
			'associations' => ['Images', 'Inventory']
		]);
		$listing_data = $return_listing_data["results"];
		Log::info("return_listing_data", $listing_data);
		$inventory = $listing_data[0]['Inventory'];
		$products = $inventory[0]['products'];
		$vars = [];
		foreach ($products as $key => $product) {
			$product['listing_id'] = $listing_id;
			$vars[] = $product;
		}
		$listing_data[0]["price_on_property"] = json_encode($inventory[0]["price_on_property"]);
		$listing_data[0]["quantity_on_property"] = json_encode($inventory[0]["quantity_on_property"]);
		$listing_data[0]["sku_on_property"] = json_encode($inventory[0]["sku_on_property"]);

		// 存储到数据库
		(new Listing())->store($listing_data);
		(new Image)->storeV2($listing_data);
		(new Inventory)->store($vars);
	}

	/**
	 * @param $listing_id
	 * @param $image_id
	 * @param $sort
	 * 重新关联图片
	 */
	public function reAssociateListingImage($listing_id, $image_id, $sort){
		$data =  [
			"params" => [
				"listing_id" =>$listing_id
			],
			"data" => [
				"listing_image_id" => $image_id,
				"rank" => $sort
			]
		];
//		$temp = Etsy::uploadListingImage($data);
	}

	/**
	 * @param $listing_id
	 * @param $image
	 * @param $sort
	 * 上传图片
	 */
	public function uploadListingImage($listing_id,  $image, $sort){
		$data =  [
			"params" => [
				"listing_id" =>$listing_id
			],
			"data" => [
				"image" => $image,
				"rank" => $sort
			]
		];
//		$temp = Etsy::uploadListingImage($data);
	}

	/**
	 * @param $listing_id
	 * @param $image_id
	 * 删除图片关联
	 */
	public function deleteListingImage($listing_id, $image_id){
		$delete_data =  [
			"params" => [
				"listing_id" =>$listing_id,
				"listing_image_id" => $image_id
			]
		];
//		$delete_return = Etsy::deleteListingImage($delete_data);
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
