<?php

namespace Api\Common\V1\Controllers;

use Api\Common\V1\Transforms\ShopTransformer;
use App\Controller;
use Dingo\Api\Http\Request;
use Common\Entities\Shop;

class ShopsController extends Controller
{
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

        return $this->response->paginator(
            $data,
            ShopTransformer::class
        );
    }

    public function store(Request $request)
    {

    }
}
