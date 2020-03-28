<?php

namespace Order\Entities;

use App\Model;
use Order\Entities\Receipt;
use Receipt\Filters\TransactionFilter;

class Transaction extends Model
{
    use TransactionFilter;

    protected $table = 'receipt_transactions';

    public function receipt()
    {
        return $this->belongsTo(Receipt::class, 'receipt_sn', 'receipt_sn');
    }

    public function consignee()
    {
        return $this->belongsTo(Consignee::class, 'receipt_sn', 'receipt_sn');
    }

    public function scopeShippedTsz($query, $shipped_tsz)
    {
        return $query->whereBetween('shipped_tsz', $shipped_tsz);
    }

    public function getVariationsAttribute()
    {
        return json_decode($this->attributes['variations'], true) ?? '';
    }

    public function getImageAttribute()
    {
        return str_replace('75x75', '300x300', $this->attributes['image']);
    }

    public function store(array $params)
    {
        $data = [];
        // 参数过滤
        foreach ($params as $param) {
            foreach ($param['Transactions'] as $key => $value) {
                $data[] = [
                    'receipt_sn' => $param['receipt_sn'],
                    'receipt_id' => $param['receipt_id'],
                    'title' => $value['title'],
                    'transaction_id' => $value['transaction_id'],
                    'listing_id' => $value['listing_id'],
                    'price' => $value['price'],
                    'quantity' => $value['quantity'],
                    'etsy_sku' => $value['product_data']['sku'],
                    'image' => $value['MainImage']['url_75x75'],
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
        }

        return self::insert($data);
    }
}
