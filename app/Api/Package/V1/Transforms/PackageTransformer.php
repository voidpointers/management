<?php

namespace Api\Package\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Package\Entities\Logistics;
use Package\Entities\Package;

class PackageTransformer extends TransformerAbstract
{
    protected $defaultIncludes = [
        'consignee',
        'logistics',
        'item',
    ];

    public function transform(Package $package)
    {
        return $package->attributesToArray();
    }

    public function includeConsignee($package)
    {
        return $this->item(
            $package->consignee,
            new ConsigneeTransformer,
            'include'
        );
    }

    public function includeLogistics($package)
    {
        return $this->item(
            $package->logistics ?? null,
            new LogisticsTransformer,
            'include'
        );
    }

    public function includeItem($package)
    {
        return $this->collection($package->item, new ItemTransformer, 'include');
    }
}
