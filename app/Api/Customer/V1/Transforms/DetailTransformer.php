<?php

namespace Api\Message\V1\Transforms;

use Api\User\V1\Transforms\UserTransformer;
use League\Fractal\TransformerAbstract;
use Message\Entities\Detail;

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
            new UserTransformer,
            'include'
        );
    }
}
