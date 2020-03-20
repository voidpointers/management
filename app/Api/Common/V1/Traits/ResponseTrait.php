<?php

namespace Api\Common\V1\Traits;

use Api\Common\V1\Transformers\UserTransformer;
use League\Fractal\Serializer\DataArraySerializer;
use Illuminate\Support\Facades\Auth;

trait ResponseTrait
{
    /**
     * 通过用户获取token
     * 
     * @return mixed
     */
    protected function respondWithUser($user)
    {
        return $this->response->item(
            $user,
            new UserTransformer(),
            function ($resource, $fractal) {
                return $fractal->setSerializer(new DataArraySerializer);
            }
        )
        ->setMeta([
            'access_token' => Auth::guard('api')->fromUser($user),
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ])
        ->setStatusCode(201);
    }

    /**
     * 通过token返回
     *
     * @param $token
     * @return mixed
     */
    protected function respondWithToken($token)
    {
        return $this->response->array([
            'access_token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => Auth::guard('api')->factory()->getTTL() * 60
        ]);
    }
}
