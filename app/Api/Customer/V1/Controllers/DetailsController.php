<?php

namespace Api\Customer\V1\Controllers;

use Api\Customer\V1\Transforms\DetailTransformer;
use App\Controller;
use Dingo\Api\Http\Request;
use Customer\Entities\Detail;

class DetailsController extends Controller
{
    protected $request;

    protected $detailTransfomer;

    protected $contextTransformer;

    /**
     * 列表
     */
    public function index(Request $request)
    {
        $messages = Detail::with([
            'user'
        ])->paginate($request->get('limit', 200));

        return $this->response->paginator(
            $messages,
            new DetailTransformer
        );
    }

    /**
     * 详情
     */
    public function show(Request $request, $conversation_id)
    {
        $messages = Detail::with(['user'])
        ->where('conversation_id', (int) $conversation_id)
        ->paginate((int) $request->get('limit', 200));

        return $this->response->paginator(
            $messages,
            new DetailTransformer
        )->addMeta('aggregates', ['order' => 1, 'mail' => 100]);
    }
}
