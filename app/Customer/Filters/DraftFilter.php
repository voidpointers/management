<?php

namespace Customer\Filters;

use App\QueryFilter;

trait DraftFilter
{
    use QueryFilter;

    public function shopId($params)
    {
        return $this->builder->where('shop_id', $params);
    }
}
