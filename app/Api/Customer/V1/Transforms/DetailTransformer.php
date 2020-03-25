<?php

namespace Api\Customer\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Customer\Entities\Detail;

class DetailTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];

    public function transform(Detail $detail)
    {
        return $detail->attributesToArray();
    }

    public function includeUser(Detail $detail)
    {
        return $this->item(
            $detail->user ?? null,
            new CustomerTransformer,
            'include'
        );
    }
}
