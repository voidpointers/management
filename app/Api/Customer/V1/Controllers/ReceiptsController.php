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

    public function message(Request $request)
    {
        $receipt_id = $request->input('receipt_id');
        $receipt = Receipt::where(['receipt_id' => $receipt_id])->first();
        if (!$receipt) {
            return $this->response->error('订单不存在', 500);
        }

        $tody = date('M d, Y');
        $data = [
            'subject' => "Re: Order #{$receipt_id} on {$tody}",
            'message' => "Invoice: https://www.etsy.com/your/orders/{$receipt_id}"
        ];

        return $this->response->array($data);
    }

    public function send(Request $request)
    {
        $receipt_id = $request->input('receipt_id');
        $receipts = Receipt::where(['receipt_id' => explode(',', $receipt_id)])->get();

        foreach ($receipts as $receipt) {
            $this->conversationRequest->sendByReceipt([
                'receipt_id' => $receipt->receipt_id,
                'shop_id' => $receipt->shop_id,
                'message' => $request->input('message'),
                'subject' => $request->input('subject')
            ]);
        }

        return ['msg' => 'success'];
    }
}
