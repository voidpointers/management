<?php

namespace Receipt\Services;

use Receipt\Entities\Consignee;
use Receipt\Entities\Receipt;
use Receipt\Entities\Transaction;

class ReceiptService
{
    public function create(array $params)
    {
        (new Receipt)->store($params);
        Transaction::store($params);
        Consignee::store($params);
    }
}
