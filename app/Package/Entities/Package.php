<?php

namespace Package\Entities;

use App\Model;
use App\QueryFilter;
use Order\Entities\Consignee;

class Package extends Model
{
    use QueryFilter;

    protected $appends = ['status_str', 'shop_title'];

    protected const STATUS = [
        1 => '新包裹',
        2 => '待打单',
        3 => '待发货',
        7 => '已取消',
        8 => '已发货',
    ];

    public function consignee()
    {
        return $this->hasOne(Consignee::class, 'receipt_sn', 'receipt_sn');
    }

    public function item()
    {
        return $this->hasMany(Item::class, 'package_sn', 'package_sn');
    }

    public function logistics()
    {
        return $this->hasOne(Logistics::class, 'package_sn', 'package_sn');
    }

    public function getStatusStrAttribute()
    {
        return self::STATUS[$this->attributes['status']];
    }

    public function getShopTitleAttribute()
    {
        return '水晶玛姬';
    }

    /**
     * Scope a query to only include popular users.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCreateTime($query, $create_time)
    {
        return $query->whereBetween('create_time', $create_time);
    }

    public function store($receipts, $uk = 'package_sn')
    {
        $packages = $items = [];

        foreach ($receipts as $receipt) {
            $package_sn = generate_uniqid();
            $packages[] = [
                'package_sn' => $package_sn,
                'receipt_sn' => $receipt->receipt_sn,
                'receipt_id' => $receipt->receipt_id,
                'status' => 1,
                'create_time' => time(),
                'update_time' => time(),
            ];
            foreach ($receipt->transaction as $value) {
                $items[] = [
                    'package_sn' => $package_sn,
                    'receipt_id' => $receipt->receipt_id,
                    'receipt_sn' => $receipt->receipt_sn,
                    'transaction_sn' => $value->id,
                    'title' => '桌游用品',
                    'en' => 'Table Game',
                    'price' => $value->price,
                    'weight' => '0.198',
                    'quantity' => $value->quantity,
                ];
            }
        }
        Package::insert($packages);
        Item::insert($items);

        return $items;
    }
}
