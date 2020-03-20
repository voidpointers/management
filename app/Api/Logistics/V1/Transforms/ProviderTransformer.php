<?php

namespace Api\Logistics\V1\Transformers;

use League\Fractal\TransformerAbstract;
use Logistics\Entities\Provider;

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
