<?php

namespace Api\Product\V1\Transforms;

use League\Fractal\TransformerAbstract;

class InventorTransformer extends TransformerAbstract
{
    public function transform($image)
    {
        if ($image) {
            return $image->attributesToArray();
        }
        return [];
    }
}
