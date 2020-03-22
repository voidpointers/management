<?php

namespace Message\Entities;

use App\Model;

class Message extends Model
{
    protected $appends = ['send_time', 'shop_name'];

    public function details()
    {
        return $this->hasMany(Detail::class, 'conversation_id', 'conversation_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'sender_id');
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
