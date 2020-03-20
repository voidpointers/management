<?php

namespace Receipt\Repositories;

use App\Repository;
use Receipt\Entities\Transaction;
use Receipt\Filters\TransactionFilter;

class TransactionRepository extends Repository
{
    use TransactionFilter;

    public function model()
    {
        return Transaction::class;
    }

    public function query()
    {
        return Transaction::query();
    }
}
