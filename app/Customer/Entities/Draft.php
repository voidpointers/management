<?php

namespace Message\Entities;

use App\Model;
use User\Entities\User;

class Draft extends Model
{
    protected $table = 'message_drafts';

    public function user()
    {
        return $this->hasOne(User::class, 'user_id', 'sender_id');
    }
}
