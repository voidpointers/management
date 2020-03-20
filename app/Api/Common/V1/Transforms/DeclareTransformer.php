<?php

namespace Api\Common\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Common\Entities\Declares;

class DeclareTransformer extends TransformerAbstract
{
    public function transform(Declares $declares)
    {
        return $declares->attributesToArray();
    }
}
