<?php

namespace Api\Receipt\V1\Controllers;

use Api\Controller;
use Api\Receipt\V1\Exports\SalesExport;
use Dingo\Api\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Receipt\Repositories\TransactionRepository;

class SalesController extends Controller
{
    protected $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
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
