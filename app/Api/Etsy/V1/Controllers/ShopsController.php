<?php

namespace Api\Etsy\V1\Controllers;

use Api\Controller;
use Dingo\Api\Http\Request;
use Shop\Requests\ShopRequest;

class ShopsController extends Controller
{
    protected $shopRequest;

    public function __construct(ShopRequest $shopRequest)
    {
        $this->shopRequest = $shopRequest;
    }

    public function index($shop_id, Request $request)
    {
        return $this->shopRequest->getShop($shop_id);
    }

    public function update($shop_id, Request $request)
    {
        $request->offsetSet('shop_id', $shop_id);
        return $this->shopRequest->updateShop($request->all());
    }
}
