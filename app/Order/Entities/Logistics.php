<?php

namespace Order\Entities;

use App\Model;

class Logistics extends Model
{
    protected $table = 'receipt_logistics';

    public function channel()
    {
        return $this->hasOne(Channel::class, 'id', 'channel_id');
    }

    public function package()
    {
        return $this->belongsTo(Package::class, 'package_sn', 'package_sn');
    }

    public function store($logistics, $channel)
    {
        $data = [];

        foreach ($logistics as $value) {
            $data[] = [
                'package_sn' => $value['package_sn'],
                'provider_id' => $channel->provider_id,
                'channel_id' => $channel->id,
                'tracking_code' => $value['tracking_code'],
                'provider' => json_encode([
                    'provider' => $channel->provider->title,
                    'channel' => $channel->title,
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
