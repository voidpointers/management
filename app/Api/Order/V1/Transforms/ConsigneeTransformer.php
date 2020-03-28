<?php

namespace Api\Order\V1\Transforms;

use League\Fractal\TransformerAbstract;

class ConsigneeTransformer extends TransformerAbstract
{
    public function transform($consignee)
    {
        if (!$consignee) {
            return [];
        }
        return $consignee->attributesToArray();
    }
}
