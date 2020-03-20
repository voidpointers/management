<?php

namespace Voidpointers\Yunexpress;

class Client
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $appKey;

    /**
     * @var string
     */
    protected $appSecret;

    /**
     * @var string
     */
    protected $client;

    /**
     * @var string
     */
    protected $headers;

    /**
     * Constructor.
     */
    public function __construct($lang = 'en-us')
    {
        foreach (config('yunexpress') as $key => $value) {
            $this->$key = $value;
        }

        $this->headers = [
            'Content-Type' => 'application/json; charset=utf8',
            'Authorization' => 'Basic ' . $this->buildToken(),
            'Accept-Language' => $lang,
            'Accept' => 'text/json',
        ];

        $this->client = new \GuzzleHttp\Client([
            'base_uri' => $this->host,
            'headers' => $this->headers,
        ]);
    }

    /**
     * Build authorization token
     *
     * @return string
     */
    protected function buildToken()
    {
        return base64_encode($this->appKey . '&' . $this->appSecret);
    }

    /**
     * Set language
     *
     * @param  string   $lang en-us / zh-cn
     * @return Client
     */
    public function setLang($lang)
    {
        return $this->headers['Accept-Language'] = $lang;
    }

    /**
     * @param array $packages
     */
    public function createOrder(array $orders)
    {
        $api = 'WayBill/CreateOrder';

        $body = ['body' => json_encode($orders)];

        $response = $this->client->post($this->host . $api, $body);
        return $this->parseResult($response->getBody());
    }

    /**
     * Get tracking info by waybill number, order number or tracking number
     *
     * @param  string  $number waybill number, order number or tracking number
     * @return array
     */
    public function getTrackInfo($order_number)
    {
        $api = 'Tracking/GetTrackInfo';

        $query = [
            'query' => [
                'OrderNumber' => $order_number,
            ],
        ];
        $response = $this->client->get($api, $query);
        return $this->parseResult($response->getBody());
    }
    
    /**
     * 打印面单
     * 
     * @param array $order_numbers
     * @return array
     */
    public function labelPrint($order_numbers = [])
    {
        $api = 'Label/Print';

        $query = [
            'body' => json_encode($order_numbers),
        ];

        $response = $this->client->post($api, $query);
        return $this->parseResult($response->getBody());
    }

    public function country()
    {
        $api = 'Common/GetCountry';
        $response = $this->client->get($api);
        return $this->parseResult($response->getBody());
    }

    /**
     * 解析结果
     *
     * @param  string      $result
     * @throws Exception
     * @return array
     */
    public function parseResult($result)
    {
        $arr = json_decode($result, true);
        if (empty($arr) || !isset($arr['Code'])) {
            throw new \Exception('Invalid response: ' . $result, 400);
        }
        if (!in_array($arr['Code'], ['0000', '5001', '1001', '1011'])) {
            if (!is_numeric($arr['Code'])) {
                $arr['code'] = '1001';
            }
            throw new \Exception($arr['Message'], $arr['Code']);
        }
        return $arr['Item'] ?? $arr['Items'];
    }
}
