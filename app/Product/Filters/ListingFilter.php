<?php

namespace Product\Filters;

use App\QueryFilter;

trait ListingFilter
{
    use QueryFilter;

    public function listingIds($params)
    {
        return $this->builder->whereIn('listing_id', explode(',', $params));
    }
}
