<?php

namespace Api\System\V1\Controllers;

use Api\Controller;
use Api\System\V1\Transforms\CountryTransformer;
use System\Repositories\CountryRepository;

class CountriesController extends Controller
{
    protected $countryRepository;

    public function __construct(CountryRepository $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    public function lists()
    {
        return $this->response->collection(
            $this->countryRepository->get(),
            new CountryTransformer
        );
    }
}
