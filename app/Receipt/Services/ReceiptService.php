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

}
