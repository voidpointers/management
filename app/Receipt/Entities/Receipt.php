<?php

namespace Receipt\Entities;

use App\Model;
use Package\Entities\Logistics;

/**
 * 收据模型
 * 
 * @author bryan <voidpointers@hotmail.com>
 */
class Receipt extends Model
{
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
        return $this->hasMany('Receipt\Entities\Transaction', 'receipt_sn', 'receipt_sn');
    }

    public function consignee()
    {
        return $this->hasOne('Receipt\Entities\Consignee', 'receipt_sn', 'receipt_sn');
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
}
