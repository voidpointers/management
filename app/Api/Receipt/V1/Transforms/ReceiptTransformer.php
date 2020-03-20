<?php

namespace Api\Receipt\V1\Transforms;

use Api\Package\V1\Transforms\LogisticsTransformer;
use League\Fractal\TransformerAbstract;
use Receipt\Entities\Receipt;

class ReceiptTransformer extends TransformerAbstract
{
    /**
     * List of resources to automatically include
     *
     * @var array
     */
    protected $defaultIncludes = [
        'consignee', 'transaction', 'logistics'
    ];

    public function transform(Receipt $receipt)
    {
        $receipts = $receipt->attributesToArray();
        $receipts['etsy_receipt_id'] = $receipt->receipt_id;
        return $receipts;
    }

    /**
     * Include Consignee
     *
     * @param Receipt $receipt
     * @return \League\Fractal\Resource\Item
     */
    public function includeConsignee(Receipt $receipt)
    {
        return $this->item(
            $receipt->consignee,
            new ConsigneeTransformer,
            'include'
        );
    }

    /**
     * Include Transaction
     *
     * @param Receipt $receipt
     * @return \League\Fractal\Resource\Item
     */
    public function includeTransaction(Receipt $receipt)
    {
        return $this->collection(
            $receipt->transaction,
            new TransactionTransformer,
            'include'
        );
    }

    public function includeLogistics($package)
    {
        return $this->item(
            $package->logistics ?? null,
            new LogisticsTransformer,
            'include'
        );
    }
}
