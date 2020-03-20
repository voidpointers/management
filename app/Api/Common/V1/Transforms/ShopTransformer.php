<?php

namespace Api\Common\V1\Transforms;

use Common\Entities\Shop;
use League\Fractal\TransformerAbstract;

class ShopTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['declare'];

    public function transform(Shop $shop)
    {
        return $shop->attributesToArray();
    }

    public function includeDeclare($shop)
    {
        return $this->item(
            $shop->declare ?? null,
            new DeclareTransformer,
            'include'
        );
    }
}
