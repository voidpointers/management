<?php

namespace Api\Shop\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Shop\Entities\Declares;

class DeclareTransformer extends TransformerAbstract
{
    public function transform(Declares $declares)
    {
        return $declares->attributesToArray();
    }
}
