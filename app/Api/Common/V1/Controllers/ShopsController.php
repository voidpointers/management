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
        'receipt' => [
            'entities' => Receipt::class,
            'where' => ['status' => 1]
        ],
        'message' => [
            'entities' => Message::class,
            'where' => ['status' => 1]
        ],
        'listing' => [
            'entities' => Listing::class,
        ],
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
                $aggregates = [];
                foreach ($this->counts as $key => $value) {
                    $instance = $factory->setEntities($value['entities'])
                    ->countGroup('shop_id', $value['where'] ?? []);

                    $aggregates[$key] = $instance[$item->shop_id]->total ?? 0;
                }
                $item->aggregates = $aggregates;
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
