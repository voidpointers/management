<?php

namespace Logistics\Entities;

use App\Model;

class Provider extends Model
{
    protected $table = 'logistics_providers';

    public function channel()
    {
        return $this->hasMany(Channel::class, 'provider_id', 'id');
    }
}
