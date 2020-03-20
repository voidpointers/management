<?php

namespace Package\Entities;

use App\Model;
use Logistics\Entities\Channel;

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
}

