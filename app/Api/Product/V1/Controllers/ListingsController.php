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

    public function __construct(
        Listing $listing,
        ListingRequest $listingRequest)
    {
        $this->listing = $listing;
        $this->listingRequest = $listingRequest;
    }
    
    public function index(Request $request)
    {
        $applay = $this->listing->apply($request);
        if (shop_id() && -1 != shop_id()) {
            $applay = $applay->where(['shop_id' => shop_id()]);
        }

        $transfomer = ListingTransformer::class;
        if ('all' == $request->get('query')) {
            $applay->with(['images']);
            $transfomer = DetailTransformer::class;
        }

        $data = $applay->orderBy('id', 'desc')
        ->paginate($request->get('limit', 30));

        return $this->response->paginator(
            $data, $transfomer
        );
    }

    public function pull(Request $request)
    {
        $data = $this->listingRequest->pull(['shop_id' => $request->input('shop_id', 0)]);

        return $this->response->array(['msg' => 'success']);
    }

    public function show($listing_id)
    {
        $data = Listing::where('listing_id', $listing_id)
        ->with(['images'])
        ->get();

        return $this->response->collection(
            $data,
            DetailTransformer::class
        );
    }

    public function update(Request $request)
    {
        $params = $request->json();

        foreach ($params as $param) {
            if (1 > $param['listing_id'] ?? 0) {
                continue;
            }
        }
        (new Listing)->store($params->all());

        return $this->response->array(['msg' => 'success']);
    }

    public function renew(Request $request)
    {
        $listing_id = $request->input('listing_id', '');
        $data = $this->listingRequest->renew($listing_id);
        return $this->response->array(['msg' => 'success']);
    }
}
