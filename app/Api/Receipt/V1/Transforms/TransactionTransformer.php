<?php

namespace Api\Receipt\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Receipt\Entities\Transaction;

class TransactionTransformer extends TransformerAbstract
{
    public function transform(Transaction $transaction)
    {
        return $transaction->attributesToArray();
    }
}
