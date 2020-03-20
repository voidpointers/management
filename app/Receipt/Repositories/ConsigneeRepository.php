<?php

namespace Receipt\Repositories;

use App\Repository;
use Receipt\Entities\Consignee;

class ConsigneeRepository extends Repository
{
    public function model()
    {
        return Consignee::class;
    }
}
