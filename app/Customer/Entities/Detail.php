<?php

namespace Customer\Entities;

use App\Model;

class Detail extends Model
{
    protected $appends = ['is_me'];

    public const CREATED_AT = null;

    public const UPDATED_AT = null;

    public function user()
    {
        return $this->hasOne(Customer::class, 'user_id', 'sender_id');
    }

    public function getImagesAttribute()
    {
        $images = json_decode($this->attributes['images'], true);
        foreach ($images as $key => $image) {
            $images[$key] = str_replace('fullxfull', '300x300', $image);
        }
        return json_encode($images);
    }

    public function getIsMeAttribute()
    {
        if (current_user() == $this->attributes['sender_id']) {
            return true;
        }
        return false;
    }
}
