<?php

namespace Order\Entities;

use App\Model;
use Order\Entities\Receipt;
use Order\Filters\TransactionFilter;

class Transaction extends Model
{
    use TransactionFilter;

    const UPDATED_AT = null;

    protected $table = 'receipt_transactions';

    protected $fillable = [
        'receipt_sn', 'receipt_id', 'transaction_id', 'listing_id', 'title', 'etsy_sku', 'local_sku',
        'image', 'price', 'quantity', 'attributes', 'variations', 'paid_tsz', 'shipped_tsz'
    ];

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

    public function store(array $params, $uk = 'transaction_id')
    {
        $data = [];
        foreach ($params as $receipt) {
            foreach ($receipt['Transactions'] as $value) {
                $data[] = $this->padding($value, $receipt);
            }
        }

        return parent::store($data, $uk);
    }

    protected function padding($params, $receipt)
    {
        $params['receipt_sn'] = $receipt['receipt_sn'];
        $params['receipt_id'] = $receipt['receipt_id'];
        $params['variations'] = json_encode(array_map(function ($variations) {
            return [
                'name' => $variations['formatted_name'],
                'value' => $variations['formatted_value'],
            ];
        }, $params['variations']));
        $params['image'] = $params['MainImage']['url_75x75'];
        $params['attributes'] = "[]";
        $params['shipped_tsz'] = $params['shipped_tsz'] ?? 0;
        $params['paid_tsz'] = $params['paid_tsz'] ?? 0;

        return $params;
    }
}
