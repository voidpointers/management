<?php

namespace Customer\Entities;

use App\Model;
use Customer\Filters\MessageFilter;

class Message extends Model
{
    use MessageFilter;

    protected $appends = ['send_time', 'shop_name'];

    public function details()
    {
        return $this->hasMany(Detail::class, 'conversation_id', 'conversation_id');
    }

    public function user()
    {
        return $this->hasOne(Customer::class, 'user_id', 'sender_id');
    }

    /**
     * @return boolean
     */
    public function getSendTimeAttribute()
    {
        return $this->update_time;
    }

    /**
     * @return boolean
     */
    public function getShopNameAttribute()
    {
        $name = [
            16333181 => 'FastestSloth',
            16407439 => 'CrystalMaggie'
        ];
        return $name[$this->shop_id];
    }

    public static function updateById($id, $params)
    {
        return self::where(['conversation_id' => $id])->update($params);
    }
}
