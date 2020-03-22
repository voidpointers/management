<?php

namespace Api\Common\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Common\Entities\Provider;

class ProviderTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['channel'];

    public function transform(Provider $provider)
    {
        return $provider->attributesToArray();
    }

    public function includeChannel($provider)
    {
        return $this->collection(
            $provider->channel,
            new ChannelTransformer,
            'include'
        );
    }
}
