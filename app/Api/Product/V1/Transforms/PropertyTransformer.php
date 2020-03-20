<?php

namespace Api\Product\V1\Transforms;

use League\Fractal\TransformerAbstract;

class PropertyTransformer extends TransformerAbstract
{
    public function transform($property)
    {
        return $property->attributesToArray();
    }
}
