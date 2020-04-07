<?php

namespace Api\Customer\V1\Controllers;

use Api\Order\V1\Transforms\ReceiptTransformer;
use App\Controller;
use Customer\Entities\Message;
use Dingo\Api\Http\Request;
use Etsy\Requests\ConversationRequest;
use Order\Entities\Receipt;

class ReceiptsController extends Controller
{
    protected $conversationRequest;

    public function __construct(ConversationRequest $conversationRequest)
    {
        $this->conversationRequest = $conversationRequest;
    }

    public function show($convo_id)
    {
        $message = Message::where(['conversation_id' => $convo_id])->first();

        return $this->response->collection(
            Receipt::where(['buyer_user_id' => $message->sender_id])->get(),
            ReceiptTransformer::class
        );
    }

    public function send(Request $request)
    {
        $receipt_id = $request->input('receipt_id');
        $message = $request->input('message');

        foreach ($receipt_id as $receipt) {
            $this->conversationRequest->sendByReceipt([
                'receipt_id' => $receipt,
                'message' => $message
            ]);
        }

        return ['msg' => 'success'];
    }
}
