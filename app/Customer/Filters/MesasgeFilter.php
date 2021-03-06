<?php

namespace Customer\Filters;

use App\QueryFilter;

trait MessageFilter
{
    use QueryFilter;

    public function shopId($params)
    {
        return $this->builder->where('shop_id', $params);
    }

    public function status($params)
    {
        return $this->builder->where('status', $params);
    }
    
    public function convoId($params)
    {
        return $this->builder->where('conversation_id', $params);
    }
    
    public function receiptId($params)
    {
        return $this->builder->where('receipt_id', $params);
    }

    public function listingId($params)
    {
        return $this->builder->where('listing_id', $params);
    }

    public function message($params)
    {
        return $this->builder->whereHas('details', function ($query) use ($params) {
            return $query->where('message', 'like', "%{$params}%");
        });
    }
}
