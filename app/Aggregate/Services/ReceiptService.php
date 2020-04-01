<?php

namespace Aggregate\Services;

use Illuminate\Support\Facades\DB;
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
        return Receipt::select('shop_id', DB::raw('COUNT(*) as total'))
        ->groupBy('shop_id')
        ->get()
        ->keyBy('shop_id');
    }
}
