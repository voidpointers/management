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

    public function receipt($params)
    {
        $receipt_id = $params->receipt_id;
        $shop_id = $params->shop_id;

        $url = "https://www.etsy.com/api/v3/ajax/shop/{$receipt_id}/mission-control/orders/convos/{$shop_id}";

        $response = $this->client->request('GET', $url);
        return json_decode((string)$response->getBody(), true);
    }
}
