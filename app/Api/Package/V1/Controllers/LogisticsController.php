<?php

namespace Api\Package\V1\Controllers;

use App\Controller;
use Common\Entities\Channel;
use Dingo\Api\Http\Request;
use Express\Services\ExpressService;
use Order\Entities\Receipt;
use Order\Services\StateMachine as ReceiptStateMachine;
use Package\Entities\Logistics;
use Package\Entities\Package;
use Package\Services\StateMachine as PackageStateMachine;

class LogisticsController extends Controller
{
    protected $expressService;

    protected $trackingService;

    protected $receiptStateMachine;

    protected $packageStateMachine;

    public function __construct(
        ExpressService $expressService,
        ReceiptStateMachine $receiptStateMachine,
        PackageStateMachine $packageStateMachine)
    {
        $this->expressService = $expressService;
        $this->receiptStateMachine = $receiptStateMachine;
        $this->packageStateMachine = $packageStateMachine;
    }

    public function lists(Request $request)
    {

    }

    /**
     * 获取跟踪信息
     * 
     * @param string $tracking_code
     * @return array
     */
    public function trackInfo($tracking_code)
    {
        return $this->expressService->trackInfo($tracking_code);
    }

    /**
     * 创建物流订单（获取运单号）
     * 
     * @param Reqeust $request
     * @param array
     */
    public function store(Request $request)
    {
        $package_sn = $request->input('package_sn');
        $channel_code = $request->input('channel', '');

        // 获取物流商信息
        $channel = Channel::where(['code' => $channel_code])
        ->with(['provider'])
        ->first();
        if ($channel) {
            return $this->response->error('当前物流不支持', 500);
        }

        // 获取package
        $packages = Package::where(['status' => 1])
        ->whereIn('package_sn', $package_sn)
        ->get();
        if ($packages->isEmpty()) {
            return $this->response->error("当前没有需要获取物流单号的包裹", 500);
        }
        $package_sn = $packages->pluck('package_sn')->toArray();

        // 请求物流接口
        $express = $this->expressService->createOrder($packages, $channel_code);

        // 物流信息入库
        (new Logistics)->store($express, $channel);

        // 更改包裹状态
        $this->packageStateMachine->operation('track', [
            'package_sn' => $package_sn
        ]);

        // (new Receipt)->updateByPackage($packages);
       
        return $this->response->array(['data' => $express]);
    }

    /**
     * 获取面单
     * 
     * @param Request $request
     * @return array
     */
    public function labels(Request $request)
    {
        $tracking_codes = $request->input('tracking_code', '');
        if (!$tracking_codes) {
            return $this->response->error('', 500);
        }

        $data = $this->expressService->labels($tracking_codes);

        // 获取包裹列表
        $logistics = Logistics::whereIn('tracking_code', $tracking_codes)
        ->whereHas('package', function ($query) {
            return $query->where(['status' => 2]);
        })->with(['package'])->get();

        if (!$logistics->isEmpty()) {
            $package_sn = ($logistics->pluck('package_sn')->toArray());
            // 更改包裹状态
            $this->packageStateMachine->operation('print', [
                'package_sn' => $package_sn
            ]);
        }

        return $this->response->array(['data' => $data]);
    }
}
