<?php

namespace Api\Order\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Order\Entities\Receipt;
use Order\Entities\Transaction;

class TransactionsController extends Controller
{
    protected $transaction;

    protected $receipt;

    public function __construct(Receipt $receipt, Transaction $transaction)
    {
        $this->transaction = $transaction;
        $this->receipt = $receipt;
    }

    public function store(Request $request)
    {
        $receipt_sn = $request->input('receipt_sn');

        // 判断订单是否补货订单
        $receipt = $this->receipt->where([
            'receipt_sn' => $receipt_sn, 'type' => 3
        ]);
        if ($receipt->isEmpty) {
            return $this->response->error('该订单不能增加商品', 500);
        }

        $this->receipt->create($request->all());
    }
}
