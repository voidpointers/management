<?php

namespace Api\Customer\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Customer\Entities\Message;

class MessageTransformer extends TransformerAbstract
{
    protected $defaultIncludes = ['user'];

    public function transform(Message $message)
    {
        return $message->attributesToArray();
    }

    public function includeUser(Message $message)
    {
        return $this->item(
            $message->user ?? null,
            new CustomerTransformer,
            'include'
        );
    }
}
