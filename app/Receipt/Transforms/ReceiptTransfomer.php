<?php

namespace Receipt\Transforms;

use Receipt\Contracts\TransformerInterface;
use Receipt\Entities\Receipt;

class ReceiptTransformer implements TransformerInterface
{
    public function transform(array $params)
    {
        Receipt::$fillable;
    }
}
