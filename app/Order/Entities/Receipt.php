<?php

namespace Order\Entities;

use App\Model;
use Package\Entities\Logistics;
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
        'remark', 'creation_tsz', 'modified_tsz', 'create_time', 'update_time', 'packup_time',
        'dispatch_time', 'close_time', 'complete_time'
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
        return $this->hasMany('Order\Entities\Transaction', 'receipt_sn', 'receipt_sn');
    }

    public function consignee()
    {
        return $this->hasOne('Order\Entities\Consignee', 'receipt_sn', 'receipt_sn');
    }

    public function logistics()
    {
        return $this->hasOne(Logistics::class, 'package_sn', 'package_sn');
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

    public function store(array $params)
    {
        $data = [];
        // 参数过滤
        foreach ($params as $key => $param) {
            foreach ($this->fillable as $fillable) {
                if ($value = $param[$fillable] ?? '') {
                    $data[$key][$fillable] = $value;
                }
            }
            $data[$key]['modified_tsz'] = $param['last_modified_tsz'] ?? 0;
            $data[$key]['create_time'] = time();
            $data[$key]['update_time'] = time();
            $data[$key]['type'] = 1;
            $data[$key]['complete_time'] = ($param['was_shipped'] ?? 0)
                ? $param['last_modified_tsz'] : 0;
            $data[$key]['seller_msg'] = $param['message_from_seller'] ?? '';
            $data[$key]['buyer_msg'] = $param['message_from_buyer'] ?? '';
            $data[$key]['buyer_msg_zh'] = '';
        }

        return self::insert($data);
    }
}