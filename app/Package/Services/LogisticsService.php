<?php

namespace Package\Services;

use Package\Entities\Logistics;
use Package\Repositories\LogisticsRepository;

class LogisticsService
{
    protected $logisticsRepository;

    public function __construct(LogisticsRepository $logisticsRepository)
    {
        $this->logisticsRepository = $logisticsRepository;
    }

    public function create($logistics, $channel)
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

        Logistics::insert($data);

        return $data;
    }
}
