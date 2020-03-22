<?php

namespace Api\Message\V1\Transforms;

use Api\User\V1\Transforms\UserTransformer;
use League\Fractal\TransformerAbstract;
use Message\Entities\Message;

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
            new UserTransformer,
            'include'
        );
    }
}
