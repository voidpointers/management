<?php

namespace Package\Filters;

use App\QueryFilter;
use Dingo\Api\Http\Request;

trait PackageFilter
{
    use QueryFilter;

    public function status($params)
    {
        $status = [
            'new' => 1, 'tracked' => 2, 'printed' => 3, 'shipped' => 8, 'closed' => 7,
        ];
        $status = $status[$params] ?? '';
        if (!$status) {
            return $this->builder;
        }

        return $this->builder->where('status', $status);
    }

    public function createTimeStart($params)
    {
        return $this->builder->where('create_time', '>=', $params);
    }

    public function createTimeEnd($params)
    {
        return $this->builder->where('create_time', '<', $params);
    }
    
    public function consignee($params)
    {
        return $this->builder->whereHas('consignee', function($query) use ($params) {
            return $query->where('name', $params);
        });
    }

    public function countryId($params)
    {
        return $this->builder->whereHas('consignee', function($query) use ($params) {
            return $query->where('country_id', $params);
        });
    }

    public function providerId($params)
    {
        return $this->builder->where('provider_id', $params);
    }

    public function channelId($params)
    {
        return $this->builder->where('channel_id', $params);
    }
}
