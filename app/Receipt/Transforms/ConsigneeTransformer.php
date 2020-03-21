<?php

namespace Receipt\Transforms;

use Receipt\Contracts\TransformerInterface;

class ConsigneeTransformer implements TransformerInterface
{
    public function transform($receipt)
    {
        return [
            'receipt_id' => $receipt['receipt_id'],
            'receipt_sn' => $receipt['receipt_sn'],
            'country_id' => $receipt['country_id'],
            'name' => $receipt['name'] ?? '',
            'state' => $receipt['state'] ?? '',
            'city' => $receipt['city'] ?? '',
            'zip' => $receipt['zip'] ?? '',
            'first_line' => $receipt['first_line'] ?? '',
            'second_line' => $receipt['second_line'] ?? '',
            'formatted_address' => $receipt['formatted_address'] ?? '',
        ];
    }
}
