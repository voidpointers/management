<?php

namespace Product\Filters;

use App\QueryFilter;

trait ListingFilter
{
    use QueryFilter;

    public function shopId($params)
    {
        return $this->builder->where('shop_id', $params);
    }

    public function listingIds($params)
    {
        return $this->builder->whereIn('listing_id', explode(',', $params));
    }
}
