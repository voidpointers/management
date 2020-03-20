<?php

namespace Api\Receipt\V1\Controllers;

use Api\Controller;
use Dingo\Api\Http\Request;
use Receipt\Repositories\ReceiptRepository;
use Receipt\Repositories\TransactionRepository;

class TransactionsController extends Controller
{
    protected $repository;

    protected $receiptRepository;

    public function __construct(
        ReceiptRepository $receiptRepository,
        TransactionRepository $repository)
    {
        $this->receiptRepository = $receiptRepository;
        $this->repository = $repository;
    }

    public function create(Request $request, $receipt_sn)
    {
        // 判断订单是否补货订单
        $receipt = $this->receiptRepository->findWhere([
            'receipt_sn' => $receipt_sn, 'type' => 3
        ]);
        if ($receipt->isEmpty) {
            return $this->response->error('该订单不能增加商品', 500);
        }

        $this->repository->create($request->all());
    }

    public function update()
    {

    }
}
