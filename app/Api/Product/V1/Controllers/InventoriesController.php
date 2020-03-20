<?php

namespace Api\Product\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Etsy\Requests\InventoryRequest;
use Product\Entities\Inventory;

class InventoriesController extends Controller
{
    protected $inventoriesRequest;

    public function __construct(InventoryRequest $inventoriesRequest)
    {
        $this->inventoriesRequest = $inventoriesRequest;
    }

    public function pull(Request $request)
    {
        $listing_id = $request->input('listing_id');
        $data = $this->inventoriesRequest->pull($listing_id);

        return $this->response->array(['msg' => 'success']);
    }

    public function update(Request $request)
    {
        $listing_id = $request->input('listing_id');
        $params = $request->json();

        (new Inventory)->store($listing_id, $params->all());

        return $this->response->array(['msg' => 'success']);
    }
}
