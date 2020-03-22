<?php

namespace Customer\Entities;

use App\Model;

class Draft extends Model
{
    protected $table = 'message_drafts';

    public function user()
    {
        return $this->hasOne(Customer::class, 'user_id', 'sender_id');
    }
}
