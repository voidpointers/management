<?php

namespace Api\Customer\V1\Controllers;

use Api\Customer\V1\Transforms\DetailTransformer;
use Api\Customer\V1\Transforms\MessageTransformer;
use App\Controller;
use Customer\Entities\Detail;
use Dingo\Api\Http\Request;
use Customer\Entities\Message;

class MessagesController extends Controller
{
    protected $message;

    public function __construct(Message $message)
    {
        $this->message = $message;       
    }

    public function index(Request $request)
    {
        $data = $this->message->apply($request)
        ->where(['shop_id' => shop_id()])
        ->with(['user'])
        ->orderBy('update_time', 'desc')
        ->paginate((int) $request->get('limit', 30));

        return $this->response->paginator(
            $data,
            new MessageTransformer
        );
    }

    public function show(Request $request, $conversation_id)
    {
        $messages = Detail::with(['user'])
        ->where('conversation_id', (int) $conversation_id)
        ->orderBy('sort', 'asc')
        ->paginate((int) $request->get('limit', 200));

        return $this->response->paginator(
            $messages,
            new DetailTransformer
        )->addMeta('aggregates', ['order' => 1, 'mail' => 100]);
    }

    public function history(int $conversation_id, Request $request)
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
        $convos = explode(',', $conversation_id);

        Message::whereIn('conversation_id', $convos)->update([
            'status' => 2, 'is_unread' => 0
        ]);

        return $this->response->array(['msg' => '转移待审核成功']);
    }

    /**
     * 转移到未处理
     */
    public function pending(Request $request)
    {
        $conversation_id = $request->input('conversation_id');
        $convos = explode(',', $conversation_id);

        Message::whereIn('conversation_id', $convos)->update(['status' => 1]);

        return $this->response->array(['msg' => '转移未处理成功']);
    }

    public function complete(Request $request)
    {
        $conversation_id = $request->input('conversation_id');
        $convos = explode(',', $conversation_id);

        Message::whereIn('conversation_id', $convos)->update([
            'status' => 8, 'is_unread' => 0
        ]);

        return $this->response->array(['msg' => '转移已提交成功']);
    }
}
