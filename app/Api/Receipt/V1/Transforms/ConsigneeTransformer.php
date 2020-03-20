<?php

namespace Api\Receipt\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Receipt\Entities\Consignee;

class ConsigneeTransformer extends TransformerAbstract
{
    public function transform(Consignee $consignee)
    {
        return $consignee->attributesToArray();
    }
}
