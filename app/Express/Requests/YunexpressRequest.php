<?php

namespace Express\Requests;

use Express\Contracts\RequestInterface;

class YunexpressRequest implements RequestInterface
{
    protected $client;

    public function __construct()
    {
        $this->client = new \Voidpointers\Yunexpress\Client();
    }

    public function trackInfo($tracking_code = '')
    {
        return $this->client->getTrackInfo($tracking_code);
    }

    public function createOrder($params = [])
    {
        return $this->client->createOrder($params);
    }

    public function labelPrint($tracking_codes = [])
    {
        return $this->client->labelPrint($tracking_codes);
    }

    public function country()
    {
        return $this->client->country();
    }
}
