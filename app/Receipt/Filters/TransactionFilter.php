<?php

namespace Receipt\Filters;

use App\QueryFilter;

trait TransactionFilter
{
    use QueryFilter;

    public function receiptSn($params)
    {
        return $this->builder->whereHas('receipt', function($query) use ($params) {
            return $query->where('receipt_sn', $params);
        });
    }

    public function etsyReceiptId($params)
    {
        return $this->builder->whereHas('receipt', function($query) use ($params) {
            return $query->where('receipt_id', $params);
        });
    }

    public function status($params)
    {
        $status = [
            'new' => 1, 'packaged' => 2, 'shipped' => 8,'closed' => 7,
        ];
        $status = $status[$params] ?? '';
        if (!$status) {
            return $this->builder;
        }

        return $this->builder->whereHas('receipt', function($query) use ($status) {
            return $query->where('status', $status);
        });
    }

    public function isFollow($params)
    {
        return $this->builder->whereHas('receipt', function($query) use ($params) {
            return $query->where('is_follow', $params);
        });
    }

    public function buyerUserId($params)
    {
        return $this->builder->whereHas('receipt', function($query) use ($params) {
            return $query->where('buyer_user_id', $params);
        });
    }

    public function createionTszStart($params)
    {
        return $this->builder->whereHas('receipt', function($query) use ($params) {
            return $query->where('creation_tsz', '>=', $params);
        });
    }

    public function createionTszEnd($params)
    {
        return $this->builder->whereHas('receipt', function($query) use ($params) {
            return $query->where('creation_tsz', '<', $params);
        });
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

    public function etsySku($params)
    {
        return $this->builder->where('etsy_sku', $params);
    }

    public function localSku($params)
    {
        return $this->builder->where('local_sku', $params);
    }
}
