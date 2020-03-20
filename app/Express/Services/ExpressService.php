<?php

namespace Express\Services;

use Express\Requests\Request;

class ExpressService
{
    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 创建物流单，获取跟踪号
     * 
     * @params array $packags
     */
    public function createOrder($packages, $channel)
    {
        $data = [];

        $orders = $this->buildOrders($packages, $channel);

        $response = $this->request->instance()->createOrder($orders);
        foreach ($response as $value) {
            if ($value['WayBillNumber'] == null) {
                throw new \RuntimeException($value['Remark']);
            }
            $data[] = [
                'package_sn' => $value['CustomerOrderNumber'],
                'tracking_code' => $value['WayBillNumber'],
                'remark' => $value['Remark'],
            ];
        }

        return $data;
    }

    public function labels($tracking_codes)
    {
        $data = [];

        $labels = $this->request->instance()->labelPrint($tracking_codes);
        foreach ($labels as $label) {
            $data[] = [
                'url' => $label['Url'],
                'orders' => array_column($label['OrderInfos'], 'CustomerOrderNumber'),
            ];
        }

        return $data;
    }

    public function trackInfo()
    {

    }

    protected function buildOrders($packages, $channel)
    {
        $orders = [];
        foreach ($packages as $package) {
            $orders[] = $this->build($package, $channel);
        }
        return $orders;
    }

    protected function build($package, $channel)
    {
        $orders = [
            'CustomerOrderNumber' => $package->package_sn,
            'ShippingMethodCode' => $channel,
            'PackageCount' => 1,
            'Weight' => '',
            'Receiver' => [
                'CountryCode' => $package->consignee->country_code,
                'FirstName' => $package->consignee->name,
                'LastName' => '',
                'Street' => str_replace('&#39;', '', 
                    $package->consignee->first_line . ' ' . $package->consignee->second_line
                ),
                'City' => $package->consignee->city,
                'City' => $package->consignee->state,
                'State' => $package->consignee->state,
                'Zip' => $package->consignee->zip,
                'Phone' => $package->consignee->phone,
            ],
        ];
        $total_weight = 0;
        $parcels = [];
        foreach ($package->item as $item) {
            $weight = $item->weight * $item->quantity;
            $parcels[] = [
                'EName' => $item->en,
                'CName' => $item->title,
                'Quantity' => $item->quantity,
                'UnitPrice' => $item->price,
                'UnitWeight' => $weight,
                'CurrencyCode' => 'USD',
            ];
            $total_weight += $weight;
        }
        $orders['Weight'] = $total_weight;
        $orders['Parcels'] = $parcels;
        return $orders;
    }
}
