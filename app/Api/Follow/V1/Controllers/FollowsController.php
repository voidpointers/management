<?php

namespace Api\Follow\V1\Controllers;

use Api\Controller;
use Api\Follow\V1\Requests\ReceiptRequest;
use Api\Receipt\V1\Transforms\ReceiptTransformer;
use Dingo\Api\Http\Request;
use Receipt\Repositories\ReceiptRepository;
use Receipt\Services\ReceiptService;
use Receipt\Services\StateMachine;

class FollowsController extends Controller
{
    protected $receiptService;

    protected $stateMachine;

    protected $repository;

    public function __construct(
        ReceiptService $receiptService,
        StateMachine $stateMachine,
        ReceiptRepository $repository)
    {
        $this->receiptService = $receiptService;
        $this->stateMachine = $stateMachine;
        $this->repository = $repository;
    }

    public function lists(Request $request)
    {
        $request->offsetSet('is_follow', 1);

        $receipts = $this->repository->apply($request)
        ->orderBy('id', 'desc')
        ->paginate($request->get('limit', 30));
        
        return $this->response->paginator(
            $receipts,
            new ReceiptTransformer
        );
    }

    public function create(ReceiptRequest $request)
    {
        $receipt_ids = json_decode($request->input('receipt_id'));

        $this->receiptService->update(['in' => ['id' => $receipt_ids]], ['is_follow' => 1]);

        return $this->response->array(['msg' => 'success']);
    }

    public function complete(ReceiptRequest $request)
    {
        $receipt_ids = json_decode($request->input('receipt_id'));

        $this->receiptService->update(['in' => ['id' => $receipt_ids]], ['is_follow' => 0]);

        return $this->response->array(['msg' => 'success']);
    }

    public function close(ReceiptRequest $request)
    {

    }
}
