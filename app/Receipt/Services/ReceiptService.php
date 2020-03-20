<?php

namespace Receipt\Services;

use Receipt\Filters\Filter;
use Receipt\Entities\Receipt;
use Receipt\Entities\Transaction;
use Receipt\Entities\Consignee;

class ReceiptService
{
    public function updateReceipt($data, $where_field = 'id', $when_field = 'id')
    {
        return Receipt::updateBatch($data, $where_field, $when_field);
    }

    public function updateTransaction($data)
    {
        return Transaction::updateBatch($data);
    }

    public function updateConsignee($data)
    {
        return Consignee::updateBatch($data);
    }

    public function listsByIds($ids)
    {
        return Receipt::whereIn('id', $ids)->with(['consignee', 'transaction'])->get();
    }

    public function lists($where)
    {
        $query = Receipt::query();

        foreach ($where as $key => $value) {
            if ('in' == $key) {
                foreach ($value as $k => $val) {
                    $query->whereIn($k, $val);
                }
            } else {
                $query->where($value);
            }
        }

        return $query->with(['consignee', 'transaction'])->get();
    }

    public function update($where, $data)
    {
        $query = Receipt::query();

        foreach ($where as $key => $value) {
            if ('in' == $key) {
                foreach ($value as $k => $val) {
                    $query->whereIn($k, $val);
                }
            } else {
                $query->where($value);
            }
        }

        return $query->update($data);
    }
}
