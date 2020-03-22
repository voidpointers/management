<?php

namespace Api\Message\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Message\Entities\Draft;

class DraftTransformer extends TransformerAbstract
{
    public function transform(Draft $draft)
    {
        return $draft->attributesToArray();
    }
}
