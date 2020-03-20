<?php

namespace Api\Etsy\V1\Controllers;

use Api\Controller;
use Dingo\Api\Http\Request;
use Receipt\Services\ReceiptService;

/**
 * 收据控制器
 */
class ReceiptsController extends Controller
{
    protected $receiptService;

    /**
     * Constructor.
     */
    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    /**
     * 拉取Etsy订单
     * 
     * @return
     */
    public function pull(Request $request)
    {
        $this->receiptService->lists();

        return ['msg' => 'success'];
    }
}
