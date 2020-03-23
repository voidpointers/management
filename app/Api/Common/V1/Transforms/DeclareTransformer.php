<?php

namespace Api\Common\V1\Transforms;

use League\Fractal\TransformerAbstract;

class DeclareTransformer extends TransformerAbstract
{
    public function transform($declares)
    {
        if ($declares) {
            return $declares->attributesToArray();
        }
        return [];
    }
}
