<?php

namespace Api\Common\V1\Controllers;

use App\Controller;
use Api\Common\V1\Transformers\ChannelTransformer;
use Common\Entities\Channel;
use Dingo\Api\Http\Request;

class ChannelsController extends Controller
{
    protected $channelRepository;

    public function __construct(Channel $channel)
    {
        $this->channel = $channel;
    }

    public function lists(Request $request)
    {
        $channels = $this->channel->paginate($request->get('limit', 30));

        return $this->response->paginator($channels, new ChannelTransformer);
    }

    public function edit(Request $request)
    {
        $ids = $request->input('id');
    }

    public function store()
    {

    }
}
