<?php

namespace Api\Package\V1\Controllers;

use App\Controller;
use Api\Package\V1\Transforms\PackageTransformer;
use Dingo\Api\Http\Request;
use Illuminate\Support\Facades\DB;
use Order\Entities\Receipt;
use Package\Entities\Package;
use Package\Services\StateMachine as PackageStateMachine;
use Order\Services\StateMachine as OrderStateMachine;

class PackagesController extends Controller
{
    protected $receipt;

    protected $package;

    protected $orderStateMachine;

    protected $packageStateMachine;

    public function __construct(
        Receipt $receipt,
        Package $package,
        OrderStateMachine $orderStateMachine,
        PackageStateMachine $packageStateMachine)
    {
        $this->receipt = $receipt;
        $this->package = $package;
        $this->orderStateMachine = $orderStateMachine;
        $this->packageStateMachine = $packageStateMachine;
    }

    /**
     * 包裹列表
     */
    public function index(Request $request)
    {
        $packages = $this->package->apply($request)
        ->with(['consignee', 'logistics', 'item' => function ($query) {
                return $query->with('transaction');
            }
        ])
        ->orderBy('id', 'desc')
        ->paginate($request->get('limit', 30));

        return $this->response->paginator($packages, new PackageTransformer);
    }

    /**
     * 打包
     */
    public function store(Request $request)
    {
        $receipt_ids = $request->input('receipt_id', '');
        if (!$receipt_ids) {
            return $this->response->error('参数错误[receipt_id为空]', 500);
        }
        $receipt_ids = json_decode($receipt_ids);

        // 获取订单列表
        $receipts = $this->receipt->where(['status' => 1])
        ->whereIn('id', $receipt_ids)
        ->get();
        if ($receipts->isEmpty()) {
            return $this->response->error('订单不存在或状态不正确', 500);
        }

        $receipt_ids = $receipts->pluck('id')->toArray();

        DB::beginTransaction();

        // 更改订单状态
        if (!$this->orderStateMachine->operation('packup', ['id' => $receipt_ids])) {
            DB::rollBack();
            return $this->response->error('订单状态更改失败', 500);
        }

        // 生成包裹
        $items = $this->package->store($receipts);
        if (!$items) {
            DB::rollBack();
            return $this->response->error('包裹生成失败', 500);
        }

        // 去重并组装receipt更新数据
        $data = [];
        foreach ($items as $item) {
            $data[$item['receipt_sn']] = [
                'receipt_sn' => $item['receipt_sn'],
                'package_sn' => $item['package_sn']
            ];
        }

        // 关联package_sn到receipt主表
        if (!$this->receipt->updateBatch($data, 'receipt_sn', 'receipt_sn')) {
            DB::rollBack();
            return $this->response->error('更新订单失败', 500);
        }

        DB::commit();

        return $this->response->array(['data' => $items]);
    }

    /**
     * 发货
     */
    public function delivery(Request $request)
    {
        $package_sn = $request->input('package_sn', '');
        if (!$package_sn) {
            return $this->response->error('参数错误[package_sn为空]', 500);
        }
        $package_sn = json_decode($package_sn);

        // 获取包裹列表
        $packages = $this->packageService->lists([
            'in' => ['package_sn' => $package_sn],
            'where' => ['status' => 3]
        ]);

        // 取出receipt_sn
        $items = [];
        foreach ($packages as $package) {
            foreach ($package->item as $item) {
                $items[] = [
                    'receipt_sn' => $item->receipt_sn,
                ];
            }
        }
        if ($packages->isEmpty()) {
            return $this->response->error("当前没有需要发货的包裹", 500);
        }
        $package_sn = $packages->pluck('package_sn')->toArray();

        // 更改包裹状态
        $this->packageStateMachine->operation('dispatch', ['package_sn' => $package_sn]);

        // 获取包裹包含订单
        $receipt_sn = array_unique(array_column($items, 'receipt_sn'));

        // 更改订单状态
        if (!$this->receiptStateMachine->operation('dispatch', ['receipt_sn' => $receipt_sn])) {
            return $this->response->error('订单状态更改失败', 500);
        }

        return $this->response->array(['data' => ['package_sn' => $package_sn]]);
    }

    public function import(Request $request)
    {

    }
}
