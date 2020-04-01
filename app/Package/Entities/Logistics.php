<?php

namespace Package\Entities;

use App\Model;
use Common\Entities\Channel;

class Logistics extends Model
{
    protected $table = 'package_logistics';

    public function channel()
    {
        return $this->hasOne(Channel::class, 'id', 'channel_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_sn', 'package_sn');
    }

    // public function store($logistics, $channel)
    /**
     * TODO 优化存储方法
     */
    public function store($logistics, $uk = 'package_sn')
    {
        $data = [];

        foreach ($logistics as $value) {
            $data[] = [
                'package_sn' => $value['package_sn'],
                // 'provider_id' => $channel->provider_id,
                // 'channel_id' => $channel->id,
                'tracking_code' => $value['tracking_code'],
                'provider' => json_encode([
                    // 'provider' => $channel->provider->title,
                    // 'channel' => $channel->title,
                ]),
                'remark' => $value['remark'] ?? '',
                'status' => 1, // 已发货
                'update_time' => 0,
                'create_time' => time(),
            ];
        }

        return self::insert($data);
    }
}

