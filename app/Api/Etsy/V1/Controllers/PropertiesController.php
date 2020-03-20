<?php

namespace Api\Etsy\V1\Controllers;

use Api\Controller;

class PropertiesController extends Controller
{
    public function index($listing_id)
    {
        return \Etsy::getAttributes([
            'params' => [
                'listing_id' => $listing_id
            ]
        ]);
    }
}
