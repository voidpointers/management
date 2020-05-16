<?php

namespace Api\Product\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Product\Entities\Listing;

class ListingTransformer extends TransformerAbstract
{
    public function transform(Listing $listing)
    {
		$listing->image_300 = transfer_image($listing->image);
		$listing->image_75 = transfer_image($listing->image, 'il_fullxfull', 'il_75x75');
        return $listing->attributesToArray();
    }
}
