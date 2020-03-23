<?php

namespace Api\Order\V1\Controllers;

use App\Controller;
use Api\Order\V1\Exports\SalesExport;
use Dingo\Api\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Order\Entities\Transaction;

class SalesController extends Controller
{
    protected $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * 导出订单
     * 
     * @return
     */
    public function export(Request $request)
    {
        $data = $this->transactionRepository->apply($request)
            ->with(['consignee', 'receipt'])
            ->orderBy('id', 'desc')
            ->get();
        
        return Excel::download(new SalesExport($data), 'receipts.xlsx');
    }
}
