<?php

namespace Api\Common\V1\Transformers;

use League\Fractal\TransformerAbstract;
use Common\Entities\Channel;

class ChannelTransformer extends TransformerAbstract
{
    public function transform(Channel $channel)
    {
        return $channel->attributesToArray();
    }
}
