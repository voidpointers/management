<?php

namespace Api\Customer\V1\Controllers;

use Api\Order\V1\Transforms\ReceiptTransformer;
use App\Controller;
use Customer\Entities\Message;
use Order\Services\ReceiptService;

class ReceiptsController extends Controller
{
    protected $receiptService;

    public function __construct(ReceiptService $receiptService)
    {
        $this->receiptService = $receiptService;
    }

    public function show($convo_id)
    {
        $message = Message::where(['conversation_id' => $convo_id])->first();

        return $this->response->collection(
            $this->receiptService(['buyer_user_id' => $message->sender_id]),
            ReceiptTransformer::class
        );
    }
}
