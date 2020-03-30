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
        return self::insert($params);
    }
}
