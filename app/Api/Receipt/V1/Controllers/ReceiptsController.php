<?php

namespace Api\Receipt\V1\Controllers;

use Api\Controller;
use Api\Receipt\V1\Exports\ReceiptsExport;
use Api\Receipt\V1\Imports\ReceiptImport;
use Api\Receipt\V1\Requests\ReceiptRequest;
use Api\Receipt\V1\Transforms\ReceiptTransformer;
use Dingo\Api\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Package\Services\PackageService;
use Receipt\Entities\Receipt;
use Receipt\Repositories\ReceiptRepository;
use Receipt\Repositories\TransactionRepository;
use Receipt\Services\ReceiptService;
use Receipt\Services\StateMachine;

/**
 * 收据控制器
 * 
 * @author bryan <voidpointers@hotmail.com>
 */
class ReceiptsController extends Controller
{
    protected $repository;

    protected $transactionRepository;

    protected $stateMachine;

    protected $packageService;

    protected $receiptService;

    public function __construct(
        ReceiptRepository $repository,
        TransactionRepository $transactionRepository,
        StateMachine $stateMachine,
        PackageService $packageService,
        ReceiptService $receiptService,
        Receipt $receipt)
    {
        $this->repository = $repository;
        $this->transactionRepository = $transactionRepository;
        $this->stateMachine = $stateMachine;
        $this->packageService = $packageService;
        $this->receiptService = $receiptService;
        $this->receipt = $receipt;
    }

    /**
     * 获取订单列表
     * 
     * @param Request $request
     * @return string
     */
    public function lists(Request $request)
    {
        $receipts = $this->repository->apply($request)
            ->with(['consignee', 'transaction', 'logistics'])
            ->orderBy('id', 'desc')
            ->paginate($request->get('limit', 30));

        return $this->response->paginator($receipts, new ReceiptTransformer);
    }

    public function create()
    {

    }

    /**
     * 关闭
     */
    public function close(Request $request)
    {
        $receipt_ids = $request->input('receipt_id', '');
        if (!$receipt_ids) {
            return $this->response->error('参数错误', 500);
        }
        $receipt_ids = json_decode($receipt_ids);

        // 更改状态
        if (!$this->stateMachine->operation('close', ['id' => $receipt_ids])) {
            return $this->response->error('订单状态更改失败', 500);
        }

        return $this->response->noContent();
    }

    /**
     * 更新
     */
    public function update(ReceiptRequest $request, $receipt_sn)
    {
        $validated = $request->validated();
        if (!$validated) {
            return $this->response->error('缺少必要参数', 500);
        }

        $this->receiptService->update(
            ['where' => ['receipt_sn' => $receipt_sn]], $validated
        );

        return $this->response->array(['msg' => 'success']);
    }

    public function copy()
    {

    }

    /**
     * 导入订单
     */
    public function import(Request $request) 
    {
        Excel::import(new ReceiptImport, $request->file('file'));
        
        return $this->response->array(['msg' => 'success']);
    }

    /**
     * 导出订单
     * 
     * @return
     */
    public function export(Request $request)
    {
        $data = $this->transactionRepository->apply($request)
            ->with(['consignee', 'receipt'])
            ->orderBy('id', 'desc')
            ->get();
        
        return Excel::download(new ReceiptsExport($data), 'receipts.xlsx');
    }
}
