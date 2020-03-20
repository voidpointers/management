<?php

namespace Api\Customization\V1\Controllers;

use Api\Controller;
use Api\Receipt\V1\Transforms\ReceiptTransformer;
use Dingo\Api\Http\Request;
use Receipt\Repositories\ReceiptRepository;
use Receipt\Services\ReceiptService;

class CustomizationController extends Controller
{
    protected $receiptService;

    public function __construct(
        ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function lists(Request $request)
    {
        $receipts = $this->receiptService->query($request)->orderBy('id', 'desc')
            ->paginate($request->get('limit', 30));
        
        return $this->response->paginator(
            $receipts,
            new ReceiptTransformer
        );
    }

    public function create(Request $request)
    {

    }

    public function complete(Request $request)
    {

    }

    public function close(Request $request)
    {

    }
}
