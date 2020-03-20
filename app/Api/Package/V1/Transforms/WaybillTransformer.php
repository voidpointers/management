<?php

namespace Api\Package\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Receipt\Entities\Receipt;

class WaybillTransformer extends TransformerAbstract
{
    public function transform(Receipt $receipt)
    {
        return [
            
        ];
    }
}
