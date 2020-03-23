<?php

namespace Api\Order\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Order\Entities\Consignee;

class ConsigneeTransformer extends TransformerAbstract
{
    public function transform(Consignee $consignee)
    {
        return $consignee->attributesToArray();
    }
}
