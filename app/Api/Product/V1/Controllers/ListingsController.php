<?php

namespace Api\Product\V1\Controllers;

use Api\Product\V1\Transforms\DetailTransformer;
use Api\Product\V1\Transforms\ListingTransformer;
use App\Controller;
use Dingo\Api\Http\Request;
use Etsy\Requests\ListingRequest;
use Product\Entities\Listing;

class ListingsController extends Controller
{
    protected $listingRequest;

    public function __construct(ListingRequest $listingRequest)
    {
        $this->listingRequest = $listingRequest;
    }
    
    public function index(Request $request)
    {
        $shop_id = $request->header('shop-id');

        $data = Listing::where(['shop_id' => $shop_id])
        ->orderBy('id', 'desc')
        ->paginate($request->get('limit', 30));

        return $this->response->paginator(
            $data,
            ListingTransformer::class
        );
    }

    public function pull(Request $request)
    {
        $data = $this->listingRequest->pull(['shop_id' => shop_id()]);

        return $this->response->array(['msg' => 'success']);
    }

    public function show(Request $request)
    {
        $shop_id = $request->header('shop-id');
        $listing_ids = $request->input('listing_ids');

        $data = Listing::where(['shop_id' => $shop_id])
        ->whereIn('listing_id', explode(',', $listing_ids))
        ->with(['images'])
        ->get();

        return $this->response->collection(
            $data,
            DetailTransformer::class
        );
    }

    public function update(Request $request)
    {
        $shop_id = $request->header('shop-id');
        $params = $request->json();

        foreach ($params as $param) {
            if (1 > $param['listing_id'] ?? 0) {
                continue;
            }
        }
        (new Listing)->store($shop_id, $params->all());

        return $this->response->array(['msg' => 'success']);
    }
}
