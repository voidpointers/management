<?php

namespace Api\Logistics\V1\Controllers;

use Api\Controller;
use Api\Logistics\V1\Transformers\ChannelTransformer;
use Dingo\Api\Http\Request;
use Logistics\Repositories\ChannelRepository;

class ChannelsController extends Controller
{
    protected $channelRepository;

    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function lists(Request $request)
    {
        $channels = $this->channelRepository->paginate($request->get('limit', 30));

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
