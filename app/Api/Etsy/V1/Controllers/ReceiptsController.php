<?php

namespace Api\Etsy\V1\Controllers;

use App\Controller;
use Dingo\Api\Http\Request;
use Etsy\Requests\ReceiptRequest;

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
        return $this->receiptRequest->filters($request->all());
    }
}
