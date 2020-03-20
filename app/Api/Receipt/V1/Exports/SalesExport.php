<?php

namespace Api\Receipt\V1\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class SalesExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
    protected const STATUS = [
        1 => '新订单',
        2 => '待发货',
        7 => '已取消',
        8 => '已发货'
    ];

    protected $receipts;

    public function __construct($receipts)
    {
        $this->receipts = $receipts;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->receipts;
    }

    public function headings(): array
    {
        return [
            'Etsy订单号',
            'Etsy SKU',
            '库存属性',
            '库存SKU',
            '购买数量',
            '订单状态',
            '下单时间',
        ];
    }

    /**
    * @var Receipt $receipt
    */
    public function map($receipt): array
    {
        return [
            $receipt->receipt_id,
            $receipt->etsy_sku,
            $receipt->attributes,
            $receipt->local_sku,
            $receipt->quantity,
            $receipt->receipt->status,
            date('Y-m-d H:i:s', $receipt->creation_tsz),
        ];
    }
}
