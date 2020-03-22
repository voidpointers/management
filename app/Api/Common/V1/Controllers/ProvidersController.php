<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use Api\Common\V1\Transformers\ProviderTransformer;
use Common\Entities\Provider;

class ProvidersController extends Controller
{
    protected $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function lists()
    {
        $providers = $this->provider->with(['channel' => function($query) {
            return $query->where('status', 1);
        }])->get();

        return $this->response->collection($providers, new ProviderTransformer);
    }
}
