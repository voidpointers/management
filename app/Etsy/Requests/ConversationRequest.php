<?php

namespace Etsy\Requests;

use GuzzleHttp\Client;

class ConversationRequest
{
    protected $client;

    public function __construct()
    {
        $this->client = new Client();
    }

    public function send($params)
    {
        $url = config('shops')[shop_id()]['spider'] . '/schedule.json';

        $response = $this->client->request('POST', $url, [
            'form_params' => [
                'convo_id' => $params['conversation_id'],
                'message' => $params['message'],
                'spider' => 'send',
                'project' => 'etsy'
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }
}
