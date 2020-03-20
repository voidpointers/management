<?php

namespace Package\Repositories;

use App\Repository;
use Package\Entities\Logistics;

class LogisticsRepository extends Repository
{
    public function model()
    {
        return Logistics::class;
    }
}
