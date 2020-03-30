<?php

namespace Order\Entities;

use App\Model;
use Common\Entities\Channel;

class Logistics extends Model
{
    protected $table = 'receipt_logistics';

    public function channel()
    {
        return $this->hasOne(Channel::class, 'id', 'channel_id');
    }

    public function store(array $params)
    {
        $logistics = self::whereIn('receipt_sn', array_column($params, 'receipt_sn'))
        ->get()
        ->all();

        $create = $update = [];

        foreach ($params as $param) {
            if (in_array($param['package_sn'], $logistics)) {
                $update[] = $param;
            } else {
                $create[] = $param;
            }
        }

        $res = false;

        if ($create) {
            $res = self::insert($params);
        }
        if ($update) {
            $res = self::updateBatch($params, 'receipt_sn', 'receipt_sn');
        }
        return $res;
    }
}
