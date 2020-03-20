<?php

namespace Api\User\V1\Transformers;

use League\Fractal\TransformerAbstract;
use User\Entities\User;

class UserTransformer extends TransformerAbstract
{
    public function transform(User $user)
    {
        return $user->attributesToArray();
    }
}
