<?php

namespace Api\Customer\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Customer\Entities\Draft;

class DraftTransformer extends TransformerAbstract
{
    public function transform(Draft $draft)
    {
        return $draft->attributesToArray();
    }
}
