<?php

namespace Etsy\Requests;

use Voidpointers\Etsy\Facades\Etsy;
use Order\Entities\Receipt;

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

        // 数据倒序排列
        $data = [];
        foreach ($receipts as $id => $value) {
            // 过滤已存在数据
            if (in_array($id, $temp)) {
                continue;
            }
            $data[$id] = $value;
            $data[$id]['receipt_sn'] = generate_uniqid();
            $data[$id]['status'] = $value['was_shipped'] ? 8 : 1;
            $data[$id]['shop_id'] = $params['shop_id'];
        }

        return $data;
    }
}
