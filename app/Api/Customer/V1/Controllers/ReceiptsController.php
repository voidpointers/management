<?php

namespace Api\Customer\V1\Controllers;

use Api\Order\V1\Transforms\ReceiptTransformer;
use App\Controller;
use Customer\Entities\Message;
use Order\Entities\Receipt;

class ReceiptsController extends Controller
{
    public function show($convo_id)
    {
        $message = Message::where(['conversation_id' => $convo_id])->first();

        return $this->response->collection(
            Receipt::where(['buyer_user_id' => $message->sender_id])->get(),
            ReceiptTransformer::class
        );
    }
}
