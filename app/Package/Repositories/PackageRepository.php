<?php

namespace Package\Repositories;

use App\Repository;
use Package\Entities\Package;
use Package\Filters\PackageFilter;

class PackageRepository extends Repository
{
    use PackageFilter;

    public function model()
    {
        return Package::class;
    }

    public function query()
    {
        return Package::query();
    }
}
