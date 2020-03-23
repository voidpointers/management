<?php

namespace Package\Services;

use Package\Entities\Item;
use Package\Entities\Logistics;
use Package\Entities\Package;
use Package\Filters\Filter;

class PackageService
{
    protected const STATUS = [
        'new' => 1, // 待获取物流跟踪号
        'tracked' => 2, // 已获取物流跟踪号，待打单
        'printed' => 3, // 已打单，待发货
        'shipped' => 8, // 已发货
        'closed' => 7,
    ];

    

    public function logistics($where)
    {
        $query = Logistics::query();

        foreach ($where as $key => $value) {
            if ('in' == $key) {
                foreach ($value as $k => $val) {
                    $query->whereIn($k, $val);
                }
            }
        }

        return $query->whereHas('package', function ($query) use ($where) {
            return $query->where($where['where']);
        })->with(['package'])->get();
    }

    public function lists($where)
    {
        $query = Package::query();

        foreach ($where as $key => $value) {
            if ('in' == $key) {
                foreach ($value as $k => $val) {
                    $query->whereIn($k, $val);
                }
            } else {
                $query->where($value);
            }
        }

        return $query->with(['consignee', 'item'])->get();
    }

    public function items($pacakge_sn)
    {
        return Item::whereIn('package_sn', $pacakge_sn)->get();
    }
}
