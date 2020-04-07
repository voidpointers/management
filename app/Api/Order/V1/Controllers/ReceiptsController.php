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

    protected const STATUS = [
        'new' => 1,
        'fllow_up' => 2,
        'followed' => 3,
        'packaged' => 4,
        'shipped' => 8,
        'closed' => 7
    ];

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
        $applay = $this->receipt->apply($request);
        if (shop_id() && -1 != shop_id()) {
            $applay = $applay->where(['shop_id' => shop_id()]);
        }

        $data = $applay->with(['consignee', 'transaction', 'logistics'])
        ->orderBy('creation_tsz', 'desc')
        ->paginate($request->get('limit', 30));

        return $this->response->paginator($data, new ReceiptTransformer);
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
    public function update($receipt_sn, Request $request)
    {
        $validated = $request->validated();
        if (!$validated) {
            return $this->response->error('缺少必要参数', 500);
        }

        $this->receipt->where(['receipt_sn' => $receipt_sn])->update($validated);

        return $this->response->array(['msg' => 'success']);
    }

    /**
     * 状态转移
     */
    public function operating(Request $request)
    {
        $receipt_sn = $request->input('receipt_sn', '');
        $status = $request->input('status');
        if (!in_array($status, array_keys(self::STATUS))) {
            return $this->response->error('状态错误', 500);
        }

        $data = [
            'status' => self::STATUS[$status],
        ];

        // 更改状态
        $query = Receipt::whereIn('receipt_sn', explode(',', $receipt_sn));

        // 查询当前状态
        foreach ($query->get() as $receipt) {
            if ($receipt->status == $data['status']) {
                return $this->response->error('不允许重复操作', 500);
            }
        }

        $query->update($data);

        return $this->response->array(['msg' => 'success']);
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
        ->with(['consignee', 'receipt'])
        ->orderBy('id', 'desc')
        ->limit(1000)
        ->get();
        
        $date = date("Ymd");

        if ('receipt' == $type) {
            $export = new ReceiptsExport($data);
            $filename = "导出订单_{$date}.xlsx";
        } else {
            $export = new SalesExport($data);
            $filename = "导出销量_{$date}.xlsx";
        }

        return Excel::download($export, $filename);
    }

    /**
     * 拉取订单
     */
    public function pull(Request $request)
    {
        $request->offsetSet('shop_id', shop_id());
        $data = $this->receiptRequest->filters($request->all());
        if (empty($data)) {
            echo "订单列表为空" . PHP_EOL;
            return;
        }

        // 入库
        (new Receipt())->store($data);
        (new Transaction())->store($data);
        (new Consignee())->store($data);

        return ['msg' => 'success'];
    }
}
