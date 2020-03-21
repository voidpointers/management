<?php

namespace Etsy\Requests;

use Voidpointers\Etsy\Facades\Etsy;
use Receipt\Entities\Receipt;

class ReceiptRequest
{
    /**
     * 获取列表
     * 
     * @param array $params
     * @return
     */
    public function receipts(array $params)
    {
        $params['shop_id'] = shop_id();

        $receipts = Etsy::findAllShopReceipts([
            'params' => $params,
            'associations' => [
                'Transactions' => ['associations' => ['MainImage']]
            ]
        ]);
        return $receipts['results'] ?? [];
    }

    /**
     * 筛选列表
     * 
     * @param array $params
     */
    public function filters(array $params)
    {
        $receipts = array_column($this->receipts($params), null, 'receipt_id');

        // 获取已入库数据
        $temp = Receipt::whereIn(
            'receipt_id', array_keys($receipts)
        )->pluck('receipt_id')->all();

        $data = array_filter($receipts, function ($value) use ($temp) {
            return !in_array($value, $temp);
        });

        $data = [];
        foreach ($receipts as $id => $value) {
            if (in_array($id, $temp)) {
                continue;
            }
            $data[$id] = $value;
            $data[$id]['status'] = $value['was_shipped'] ? 8 : 1;
        }

        // 数据倒序排列
        return array_reverse($data);
    }
}