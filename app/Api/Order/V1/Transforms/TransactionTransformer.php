<?php

namespace Api\Order\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Order\Entities\Transaction;

class TransactionTransformer extends TransformerAbstract
{
    public function transform(Transaction $transaction)
    {
        return $transaction->attributesToArray();
    }
}
