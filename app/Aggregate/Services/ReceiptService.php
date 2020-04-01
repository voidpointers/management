<?php

namespace Aggregate\Services;

use Common\Entities\Shop;
use Order\Entities\Receipt;

class ReceiptService
{
    protected $receipt;

    public function __construct(Receipt $receipt)
    {
        $this->receipt = $receipt;
    }

    public function count(array $params = [])
    {
        return Shop::where(['status' => 1])
        ->groupBy('shop_id')
        ->count()
        ->keyBy('shop_id');
    }
}
