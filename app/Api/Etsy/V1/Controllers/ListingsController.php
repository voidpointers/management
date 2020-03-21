<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Listing\Services\ListingService;
use Voidpointers\Etsy\Facades\Etsy;

/**
 * 产品控制器
 */
class ListingsController extends Controller
{
    public function lists(Request $request)
    {
        return Etsy::findAllListingActive([
            'params' => $request->all(),
            'associations' => ['Images']
        ]);
    }

    public function inventory($listing_id)
    {
        return Etsy::getInventory([
            'params' => [
                'listing_id' => $listing_id
            ]
        ]);
    }
}
