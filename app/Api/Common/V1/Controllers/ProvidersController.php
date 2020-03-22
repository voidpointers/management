<?php

namespace Api\Logistics\V1\Controllers;

use Api\Controller;
use Api\Logistics\V1\Transformers\ProviderTransformer;
use Logistics\Repositories\ProviderRepository;

class ProvidersController extends Controller
{
    protected $providerRepository;

    public function __construct(ProviderRepository $providerRepository)
    {
        $this->providerRepository = $providerRepository;
    }

    public function lists()
    {
        $providers = $this->providerRepository->with(['channel' => function($query) {
            return $query->where('status', 1);
        }])->get();

        return $this->response->collection($providers, new ProviderTransformer);
    }
}
