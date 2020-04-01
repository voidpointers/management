<?php

namespace Api\Common\V1\Controllers;

use Aggregate\Services\AggregateFactory;
use Api\Common\V1\Transforms\ShopTransformer;
use App\Controller;
use Dingo\Api\Http\Request;
use Common\Entities\Shop;
use Customer\Entities\Message;
use Order\Entities\Receipt;
use Product\Entities\Listing;

class ShopsController extends Controller
{
    protected $counts = [
        'count_receipts' => Receipt::class,
        'count_messages' => Message::class,
        'count_listings' => Listing::class,
    ];

    public function index(Request $request)
    {
        $query = $request->input('query', '');

        $columns = ['*'];
        if ('few' == $query) {
            $columns = ['shop_id', 'shop_name', 'user_id', 'username', 'icon'];
        }

        $data = Shop::where(['status' => 1])
        ->select($columns)
        ->paginate($request->get('limit', 30));

        if ('few' != $query) {
            $factory = new AggregateFactory();
            
            $data->each(function ($item) use ($factory) {
                foreach ($this->counts as $key => $entities) {
                    $aggregates = $factory->setEntities($entities)->count();
                    $item->$key = $aggregates[$item->shop_id]->total ?? 0;
                }
            });
        }

        return $this->response->paginator(
            $data,
            ShopTransformer::class
        );
    }

    public function store(Request $request)
    {

    }
}
