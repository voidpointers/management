<?php

namespace Api\Customer\V1\Transforms;

use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    public function transform($customer)
    {
        if (!$customer) {
            return [];
        }
        return $customer->attributesToArray();
    }
}
