<?php

namespace Api\Common\V1\Transforms;

use Common\Entities\Country;
use League\Fractal\TransformerAbstract;

class CountryTransformer extends TransformerAbstract
{
    public function transform(Country $country)
    {
        return $country->attributesToArray();
    }
}
