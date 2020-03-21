<?php

namespace Receipt\Transforms;

use Receipt\Contracts\TransformerInterface;

class TransactionTransformer implements TransformerInterface
{
    public function transform($receipts)
    {
        $data = [];
        foreach ($receipts['Transactions'] as $value) {
            $data[] = [
                'title' => $value['title'],
                'receipt_id' => $receipts['receipt_id'],
                'receipt_sn' => $receipts['receipt_sn'],
                'transaction_sn' => $value['transaction_id'],
                'listing_id' => $value['listing_id'],
                'etsy_sku' => $value['product_data']['sku'],
                'image' => $value['MainImage']['url_75x75'],
                'price' => $value['price'],
                'quantity' => $value['quantity'],
                'attributes' => "[]",
                'variations' => json_encode(array_map(function ($variations) {
                    return [
                        'name' => $variations['formatted_name'],
                        'value' => $variations['formatted_value'],
                    ];
                }, $value['variations'])),
                'paid_tsz' => $value['paid_tsz'] ?? 0,
                'shipped_tsz' => $value['shipped_tsz'] ?? 0
            ];
        }
        return $data;
    }
}
