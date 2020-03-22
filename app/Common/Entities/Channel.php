<?php

namespace Common\Entities;

use App\Model;

class Channel extends Model
{
    protected $table = 'logistics_channels';

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_id', 'id');
    }
}
