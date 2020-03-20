<?php

namespace Package\Entities;

use App\Model;
use Receipt\Entities\Transaction;

class Item extends Model
{
    protected $table = 'package_items';

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'id', 'transaction_sn');
    }
}
