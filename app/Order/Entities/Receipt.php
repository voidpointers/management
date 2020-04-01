<?php

namespace Order\Entities;

use App\Model;
use Order\Filters\ReceiptFilter;

/**
 * 收据模型
 * 
 * @author bryan <voidpointers@hotmail.com>
 */
class Receipt extends Model
{
    use ReceiptFilter;

    protected $fillable = [
        'receipt_sn', 'receipt_id', 'shop_id', 'type', 'order_id', 'seller_user_id', 'buyer_user_id',
        'buyer_email', 'status', 'package_sn', 'payment_method',
        'total_price', 'subtotal', 'grandtotal', 'adjusted_grandtotal', 'total_tax_cost',
        'total_vat_cost', 'total_shipping_cost', 'seller_msg', 'buyer_msg', 'buyer_msg_zh',
        'remark', 'creation_tsz', 'modified_tsz'
    ];

    protected $appends = ['status_str'];

    protected const STATUS = [
        1 => '新订单',
        2 => '待发货',
        7 => '已取消',
        8 => '已发货',
    ];

    protected const SPEED = [
        0 => '标快',
        1 => '平邮',
        2 => '加快'
    ];

    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'receipt_sn', 'receipt_sn');
    }

    public function consignee()
    {
        return $this->hasOne(Consignee::class, 'receipt_sn', 'receipt_sn');
    }

    public function logistics()
    {
        return $this->hasOne(Logistics::class, 'receipt_sn', 'receipt_sn');
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreationTsz($query, $creation_tsz)
    {
        return $query->whereBetween('creation_tsz', $creation_tsz);
    }

    public function getStatusStrAttribute()
    {
        return self::STATUS[$this->attributes['status']] ?? '';
    }

    public function listsByIds($ids)
    {
        return self::whereIn('id', $ids)->with(['consignee', 'transaction'])->get();
    }

    public function lists($where)
    {
        $query = self::query();

        foreach ($where as $key => $value) {
            if ('in' == $key) {
                foreach ($value as $k => $val) {
                    $query->whereIn($k, $val);
                }
            } else {
                $query->where($value);
            }
        }

        return $query->with(['consignee', 'transaction'])->get();
    }

    public function updateByPackage($params)
    {
        $receipts = [];
        // 提取订单
        foreach ($params as $package) {
            foreach ($package->item as $item) {
                $receipts[$item->receipt_sn] = [
                    'receipt_sn' => $item->receipt_sn,
                ];
            }
        }

        return Receipt::updateBatch($receipts, 'receipt_sn', 'receipt_sn');
    }

    public function store(array $params, $uk = 'receipt_id')
    {
        $data = array_map(function ($item) {
            return $this->padding($item);
        }, $params);
        return parent::store($data, $uk);
    }

    protected function padding($params)
    {
        $was_shipped = $params['was_shipped'] ?? 0;

        $params['status'] = $was_shipped ? 8 : 1;
        $params['shop_id'] = $params['shop_id'];
        $params['modified_tsz'] = $params['last_modified_tsz'] ?? 0;
        $params['create_time'] = time();
        $params['update_time'] = time();
        $params['type'] = 1;
        $params['complete_time'] = $was_shipped
            ? $params['last_modified_tsz'] : 0;
        $params['seller_msg'] = $params['message_from_seller'] ?? '';
        $params['buyer_msg'] = $params['message_from_buyer'] ?? '';
        $params['buyer_msg_zh'] = '';

        return $params;
    }
}
