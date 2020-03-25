<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use Api\Common\V1\Transforms\ProviderTransformer;
use Common\Entities\Provider;
use Dingo\Api\Http\Request;

class ProvidersController extends Controller
{
    protected $provider;

    public function __construct(Provider $provider)
    {
        $this->provider = $provider;
    }

    public function index(Request $request)
    {
        $providers = $this->provider->with(['channel' => function($query) {
            return $query->where('status', 1);
        }])->paginate($request->get('limit', 30));

        return $this->response->paginator($providers, new ProviderTransformer);
    }
}
