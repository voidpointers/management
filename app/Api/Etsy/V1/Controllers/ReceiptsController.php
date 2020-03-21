<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Etsy\Requests\ReceiptRequest;
use Receipt\Entities\Receipt;

/**
 * 收据控制器
 */
class ReceiptsController extends Controller
{
    protected $receiptRequest;

    public function __construct(ReceiptRequest $receiptRequest)
    {
        $this->receiptRequest = $receiptRequest;
    }

    public function index(Request $request)
    {
        $receipts = $this->receiptRequest->filters($request->all());
        return (new Receipt)->store($receipts);
    }
}
