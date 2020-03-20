<?php

namespace Api\System\V1\Transforms;

use League\Fractal\TransformerAbstract;
use System\Entities\Country;

class CountryTransformer extends TransformerAbstract
{
    public function transform(Country $country)
    {
        return $country->attributesToArray();
    }
}
