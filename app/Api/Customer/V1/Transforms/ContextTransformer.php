<?php

namespace Api\Customer\V1\Transforms;

use League\Fractal\TransformerAbstract;

class ContextTransformer extends TransformerAbstract
{
    public function transform($context)
    {
        if (!$context) {
            return [];
        }
        return $context->attributesToArray();
    }
}
