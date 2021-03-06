<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use Api\Common\V1\Transforms\CountryTransformer;
use Common\Entities\Country;

class CountriesController extends Controller
{
    protected $country;

    public function __construct(Country $country)
    {
        $this->country = $country;
    }

    public function index()
    {
        $data = $this->country->get();

        return $this->response->collection(
            $data,
            new CountryTransformer
        );
    }
}
