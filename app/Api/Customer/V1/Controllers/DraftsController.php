<?php

namespace Api\Message\V1\Controllers;

use Api\Message\V1\Requests\ConversationIdRequest;
use Api\Message\V1\Transforms\DraftTransformer;
use App\Controller;
use Dingo\Api\Http\Request;
use Etsy\Requests\ConversationRequest;
use Customer\Entities\Draft;
use Customer\Entities\Message;

class DraftsController extends Controller
{
    protected $conversationRequest;

    public function __construct(
        ConversationRequest $conversationRequest)
    {
        $this->conversationRequest = $conversationRequest;
    }

    public function show($conversation_id, Request $request)
    {
        $message = $this->info($conversation_id, [1, 2]);

        return $this->response->item(
            $message ?? new Draft(),
            DraftTransformer::class
        );
    }

    public function store(ConversationIdRequest $request)
    {
        $shop_id = $request->header('shop_id');
        $conversation_id = $request->input('conversation_id', 0);
        $message = $request->post('message', '');
        if (1 > strlen($message)) {
            return $this->response->error('消息不能为空', 500);
        }

        $draft = $this->info($conversation_id, [2]);
        if ($draft) {
            return $this->response->error('有草稿正在发送中，请稍等', 502);
        }

        // 存储消息
        $draft = Draft::updateOrCreate(
            ['shop_id' => $shop_id, 'conversation_id' => $conversation_id, 'status' => 1],
            ['message' => $message, 'sender_id' => 125136382, 'images' => '[]']
        );

        // 消息更新为已读
        Message::where([
            'conversation_id' => $conversation_id,
            'shop_id' => $shop_id
        ])->update(['is_unread' => 0]);

        return $this->response->item($draft, new DraftTransformer);
    }

    public function approve(Request $request)
    {
        $message = $request->input('message', '');
        if (!$message) {
            Message::where([
                'conversation_id' => (int) $request->input('conversation_id'),
                'shop_id' => shop_id(),
            ])->update([
                'status' => 8, 'is_unread' => 0
            ]);

            return $this->response->array(['msg' => 'success']);
        }
        
        $this->send($request->all());

        return $this->response->array(['msg' => 'success']);
    }

    /**
     * 审核通过
     */
    public function approveV2(Request $request)
    {
        $shop_id = shop_id();

        $conversation_id = $request->input('conversation_id');
        $ids = explode(',', $conversation_id);

        $msg = [];

        foreach ($ids as $id) {
            $draft = $this->info($id, [1, 2]);
            if (!$draft) {
                Message::where(['shop_id' => $shop_id, 'conversation_id' => (int) $id])->update([
                    'status' => 8, 'is_unread' => 0
                ]);
                continue;
            }
            if (1 != $draft->status) {
                $msg[$id] = '没有待发送草稿';
                continue;
            }
            // 发送前增加处理中状态，防止重复提交
            Draft::where('id', $draft->id)->update([
                'status' => 2
            ]);
            $this->send($draft);
            $msg[$id] = '发送成功';
        }

        return $this->response->array(['msg' => $msg]);
    }

    /**
     * 同步到Etsy
     */
    protected function send($params)
    {
        // 推送到爬虫
        $response = $this->conversationRequest->send($params);

        $status = 'ok' == $response['status'] ? 8 : 7;

        // 批量更新消息
        Message::where([
            'conversation_id' => (int) $params['conversation_id'],
            'shop_id' => shop_id()
        ])
        ->update([
            'status' => $status,
        ]);

        // 更新草稿
        if ($params['id'] ?? 0) {
            Draft::where(['id' => $params['id']])->update(['status' => $status]);
        }

        return $response;
    }

    /**
     * 驳回
     */
    public function reject(Request $request)
    {
        $conversation_id = $request->input('conversation_id');
        $convos = explode(',', $conversation_id);

        foreach ($convos as $id) {
            $this->check($id);
            Message::updateById($id, ['status' => 1]);
        }

        return $this->response->array(['msg' => '驳回成功过']);
    }

    /**
     * 检查
     */
    protected function check($conversation_id)
    {
        $draft = $this->info($conversation_id);
        
        if (!$draft) {
            return $this->response->error('数据不能为空', 500);
        }
        if (1 != $draft->status) {
            return $this->response->error('状态错误', 500);
        }
        return $draft;
    }

    protected function info($conversation_id, $status = [])
    {
        $where = [
            'shop_id' => shop_id(),
            'conversation_id' => $conversation_id
        ];

        $query = Draft::query()->where($where);

        if (!empty($status)) {
            $query->whereIn('status', $status);
        }

        return $query->orderBy('id', 'desc')->first();
    }
}
