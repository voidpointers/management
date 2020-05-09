<?php

namespace Api\Customer\V1\Controllers;

use Aggregate\Services\AggregateFactory;
use Api\Customer\V1\Transforms\DetailTransformer;
use Api\Customer\V1\Transforms\MessageTransformer;
use App\Controller;
use Customer\Entities\Detail;
use Dingo\Api\Http\Request;
use Customer\Entities\Message;
use Order\Entities\Receipt;

class MessagesController extends Controller
{
    protected $counts = [
        'order' => ['entities' => Receipt::class, 'field' => 'buyer_user_id'],
        'mail' => ['entities' => Message::class, 'field' => 'sender_id']
    ];

    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;       
    }

    public function index(Request $request)
    {
        $applay = $this->message->apply($request);
        if (shop_id()) {
            $applay = $applay->where(['shop_id' => shop_id()]);
        }

        $data = $applay->with(['user'])
        ->orderBy('update_time', 'desc')
        ->paginate((int) $request->get('limit', 30));

        return $this->response->paginator(
            $data,
            new MessageTransformer
        );
    }

    public function show(Request $request, $conversation_id)
    {
        $message = Message::where(['conversation_id' => $conversation_id])->first();

        $details = Detail::with(['user'])
        ->where('conversation_id', (int) $conversation_id)
        ->orderBy('sort', 'asc')
        ->paginate((int) $request->get('limit', 200));

        $factory = new AggregateFactory();
        $aggregates = [];
        foreach ($this->counts as $key => $value) {
            $count = $factory->setEntities($value['entities'])
            ->countBy([$value['field'] => $message->sender_id]);
            if ('mail' == $key) $count = 1 < $count ? $count - 1 : 0;

            $aggregates[$key] = $count;
        }

        return $this->response->paginator(
            $details,
            new DetailTransformer
        )->addMeta('aggregates', $aggregates);
    }

    public function history(Request $request, $conversation_id)
    {
        $message = Message::where('conversation_id', $conversation_id)->first();
        if (!$message) {
            return $this->response->error('对话不存在', 500);
        }
        $user_id = $message->sender_id;

        $histories = Message::with(['user'])
        ->where('conversation_id', '!=', $conversation_id)
        ->where('sender_id', $user_id)
        ->orderBy('update_time', 'desc')
        ->paginate($request->input('limit', 30));

        return $this->response->paginator(
            $histories,
            new MessageTransformer
        );
    }

    /**
     * 转移到待审核
     */
    public function review(Request $request)
    {
        $conversation_id = $request->input('conversation_id');
        if (1 > ($shop_id = shop_id())) {
            return $this->response->error('缺少店铺ID', 500);
        }

        Message::where(['shop_id', $shop_id])
        ->whereIn('conversation_id', explode(',', $conversation_id))
        ->update(['status' => 2]);

        return $this->response->array(['msg' => '转移待审核成功']);
    }

    /**
     * 转移到未处理
     */
    public function pending(Request $request)
    {
        $conversation_id = $request->input('conversation_id');
        if (1 > ($shop_id = shop_id())) {
            return $this->response->error('缺少店铺ID', 500);
        }

        Message::where('shop_id', $shop_id)
        ->whereIn('conversation_id', explode(',', $conversation_id))
        ->update(['status' => 1]);

        return $this->response->array(['msg' => '转移未处理成功']);
    }

    public function complete(Request $request)
    {
        $conversation_id = $request->input('conversation_id');
        if (1 > ($shop_id = shop_id())) {
            return $this->response->error('缺少店铺ID', 500);
        }

        Message::where(['shop_id', $shop_id])
        ->whereIn('conversation_id', explode(',', $conversation_id))
        ->update(['status' => 8, 'is_unread' => 0]);

        return $this->response->array(['msg' => '转移已提交成功']);
    }
}
