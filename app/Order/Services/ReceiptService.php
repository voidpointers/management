<?php

namespace Order\Services;

use Order\Entities\Receipt;

class ReceiptService
{
    public function lists(array $params)
    {
        return Receipt::where($params)->get();
    }
}
