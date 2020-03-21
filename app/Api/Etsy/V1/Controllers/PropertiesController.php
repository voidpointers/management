<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Voidpointers\Etsy\Facades\Etsy;

class PropertiesController extends Controller
{
    public function index($listing_id)
    {
        return Etsy::getAttributes([
            'params' => [
                'listing_id' => $listing_id
            ]
        ]);
    }
}
