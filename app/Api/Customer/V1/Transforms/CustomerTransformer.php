<?php

namespace Api\Customer\V1\Transforms;

use Customer\Entities\Customer;
use League\Fractal\TransformerAbstract;

class CustomerTransformer extends TransformerAbstract
{
    public function transform(Customer $customer)
    {
        return $customer->attributesToArray();
    }
}
