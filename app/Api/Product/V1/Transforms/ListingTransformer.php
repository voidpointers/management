<?php

namespace Api\Product\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Product\Entities\Listing;

class ListingTransformer extends TransformerAbstract
{
    public function transform(Listing $listing)
    {
        return $listing->attributesToArray();
    }
}
