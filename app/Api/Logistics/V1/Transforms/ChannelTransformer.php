<?php

namespace Api\Logistics\V1\Transformers;

use League\Fractal\TransformerAbstract;
use Logistics\Entities\Channel;

class ChannelTransformer extends TransformerAbstract
{
    public function transform(Channel $channel)
    {
        return $channel->attributesToArray();
    }
}
