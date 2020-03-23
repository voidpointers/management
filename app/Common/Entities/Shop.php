<?php

namespace Common\Entities;

use App\Model;

class Shop extends Model
{
    protected $hidden = ['access_token', 'access_secret'];

    protected $table = 'shops';

    public function declare()
    {
        return $this->hasOne(Declares::class, 'shop_id', 'shop_id');
    }
}
