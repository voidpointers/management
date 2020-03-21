<?php

namespace Receipt\Services;

use Receipt\Entities\Consignee;
use Receipt\Entities\Receipt;
use Receipt\Entities\Transaction;

class ReceiptService
{
    public function create(array $params)
    {
        Receipt::store($params);
        Transaction::insert($params['transaction']);
        Consignee::insert($params['consignee']);
    }
}
