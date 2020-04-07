<?php

namespace Etsy\Requests;

use GuzzleHttp\Client;

class ConversationRequest
{
    protected $client;

    protected $url;

    public function __construct()
    {
        $this->client = new Client();
        $this->url = config('shops')[shop_id()]['spider'] . '/schedule.json';
    }

    public function send($params)
    {
        $response = $this->client->request('POST', $this->url, [
            'form_params' => [
                'convo_id' => $params['conversation_id'],
                'message' => $params['message'],
                'spider' => 'send',
                'project' => 'etsy'
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    public function sendByReceipt($params)
    {
        $response = $this->client->request('POST', $this->url, [
            'form_params' => [
                'receipt_id' => $params['receipt_id'],
                'message' => $params['message'],
                'spider' => 'new',
                'project' => 'etsy'
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }
}
