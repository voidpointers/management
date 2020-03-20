<?php

namespace Api\Shop\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Shop\Entities\Shop;

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
