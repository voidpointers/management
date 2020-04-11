<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Product\Entities\Listing as EntitiesListing;
use Voidpointers\Etsy\Facades\Etsy;

class Listing extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'listing:pull';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拉取国家列表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() 
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $page = 1;
        $shop_id = 19428611;
        // $shop_id = 16407439;

        while ($page) {
            $listings = $this->listings($shop_id, $page);

            $data = [];
            foreach ($listings['results'] as $value) {
                $data[] = [
                    'shop_id' => $shop_id,
                    'taxonomy_id' => $value['taxonomy_id'],
                    'listing_id' => $value['listing_id'],
                    'title' => $value['title'],
                    'taxonomy_path' => json_encode($value['taxonomy_path']),
                    'tags' => json_encode($value['tags']),
                    'views' => $value['views'],
                    'num_favorers' => $value['num_favorers'],
                    'url' => $value['url'],
                    'image' => $value['Images'][0]['url_570xN'],
                    'inventories' => json_encode($this->inventory($value['listing_id'])),
                    'creation_tsz' => $value['creation_tsz']
                ];
            }

            DB::table('listing_stats')->insert($data);

            echo '第' . $page . '页处理完成' . PHP_EOL;
            // 最后一页为null，退出循环
            $page = $listings['pagination']['next_page'];
        }
    }

    public function listings($shop_id = 16407439, $page = 1)
    {
        $data = Etsy::findAllShopListingsActive([
            'params' => ['shop_id' => $shop_id, 'page' => $page],
            'associations' => ['Images']
        ]);
        return $data;
    }

    public function inventory($listing_id)
    {
        $inventory = Etsy::getInventory([
            'params' => [
                'listing_id' => $listing_id
            ]
        ]);
        $products = $inventory['results']['products'];

        $data = [];
        foreach ($products as $value) {
            $price = $value['offerings'][0]['price']['currency_formatted_raw'];
            $data[] = [
                'property_name' => $value['property_values'][0]['property_name'] ?? '',
                'price' => number_format($price * 7, 2),
                'quantity' => $value['offerings']['quantity'] ?? 0
            ];
        }
        return $data;
    }
}
