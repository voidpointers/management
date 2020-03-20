<?php

namespace Receipt\Repositories;

use App\Repository;
use Receipt\Contracts\ReceiptInterface;
use Receipt\Entities\Receipt;
use Receipt\Filters\ReceiptFilter;

/**
 * 收据仓库
 * 
 * @author bryan <voidpointers@hotmail.com>
 */
class ReceiptRepository extends Repository implements ReceiptInterface
{
    use ReceiptFilter;

    protected $fieldSearchable = [
        'receipt_id',
        'creation_tsz'
    ];

    public function model()
    {
        return Receipt::class;
    }

    public function query()
    {
        return Receipt::query();
    }
}
