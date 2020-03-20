<?php

namespace Api\Package\V1\Transforms;

use League\Fractal\TransformerAbstract;
use Package\Entities\Item;

class ItemTransformer extends TransformerAbstract
{
    public function transform(Item $item)
    {
        $transaction = $item->transaction;

        return [
            'transaction_id' => $transaction->transaction_id,
            'tile' => $transaction->title,
            'image' => $transaction->image,
            'etsy_sku' => $transaction->etsy_sku,
            'local_sku' => $transaction->local_sku,
            'quantity' => $transaction->quantity
        ];
    }
}
