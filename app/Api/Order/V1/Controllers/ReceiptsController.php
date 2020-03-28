<?php

namespace Api\Order\V1\Controllers;

use App\Controller;
use Api\Order\V1\Exports\ReceiptsExport;
use Api\Order\V1\Exports\SalesExport;
use Api\Order\V1\Imports\ReceiptImport;
use Api\Order\V1\Transforms\ReceiptTransformer;
use Dingo\Api\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Order\Entities\Consignee;
use Order\Entities\Receipt;
use Order\Entities\Transaction;
use Order\Services\StateMachine;
use Etsy\Requests\ReceiptRequest;

/**
 * 收据控制器
 * 
 * @author bryan <voidpointers@hotmail.com>
 */
class ReceiptsController extends Controller
{
    protected $receiptRequest;

    protected $stateMachine;

    protected $receipt;

    protected $transaction;

    public function __construct(
        ReceiptRequest $receiptRequest,
        Transaction $transaction,
        Receipt $receipt,
        StateMachine $stateMachine)
    {
        $this->receiptRequest = $receiptRequest;
        $this->transaction = $transaction;
        $this->stateMachine = $stateMachine;
        $this->receipt = $receipt;
    }

    /**
     * 获取订单列表
     * 
     * @param Request $request
     * @return string
     */
    public function index(Request $request)
    {
        $receipts = $this->receipt->apply($request)
        ->where(['shop_id' => shop_id()])
        ->with(['consignee', 'transaction', 'logistics'])
        ->orderBy('creation_tsz', 'desc')
        ->paginate($request->get('limit', 30));

        return $this->response->paginator($receipts, new ReceiptTransformer);
    }

    /**
     * 获取详情
     * 
     * @param string $receipt_sn
     * @return
     */
    public function show($receipt_sn)
    {
        $receipt = $this->receipt->where(['receipt_sn' => $receipt_sn])
        ->with(['consignee', 'transaction', 'logistics'])
        ->orderBy('id', 'desc')
        ->first();

        return $this->response->item($receipt, new ReceiptTransformer);
    }

    /**
     * 更新
     */
    public function update($receipt_sn, ReceiptRequest $request)
    {
        $validated = $request->validated();
        if (!$validated) {
            return $this->response->error('缺少必要参数', 500);
        }

        $this->receipt->where(['receipt_sn' => $receipt_sn])->update($validated);

        return $this->response->array(['msg' => 'success']);
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
    public function export(Request $request, $type = 'receipt')
    {
        $data = $this->transaction->apply($request)
        ->where(['shop_id', shop_id()])
        ->with(['consignee', 'receipt'])
        ->orderBy('id', 'desc')
        ->get();
        
        if ('receipt' == $type) {
            $export = new ReceiptsExport($data);
        } else {
            $export = new SalesExport($data);
        }

        return Excel::download($export, 'receipts.xlsx');
    }

    public function pull(Request $request)
    {
        $request->offsetExists('shop_id', shop_id());
        $data = $this->receiptRequest->filters($request->all());
        if (empty($data)) {
            echo "订单列表为空" . PHP_EOL;
            return;
        }

        // 入库
        (new Receipt())->store($data);
        (new Transaction())->store($data);
        (new Consignee())->store($data);
    }
}
