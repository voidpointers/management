<?php

namespace Logistics\Repositories;

use App\Repository;
use Logistics\Entities\Provider;

class ProviderRepository extends Repository
{
    public function model()
    {
        return Provider::class;
    }
}
