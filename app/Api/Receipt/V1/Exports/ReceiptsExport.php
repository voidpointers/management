<?php

namespace Api\Receipt\V1\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Excel;

class ReceiptsExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize
{
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
            '订单号',
            'SKU',
            '属性',
            '数量',
            '单价',
            '币种',
            '买家姓名',
            '地址1',
            '地址2',
            '城市',
            '省/州',
            '国家二字码',
            '邮编',
            '电话',
            '手机',
            'E-mail',
            '税号',
            '门牌号',
            '公司名',
            '订单备注',
            '图片网址',
            '售出连接',
            '中文报关名',
            '英文报关名',
            '申报金额',
            '申报重量',
            '海关编码',
            '报关属性',
        ];
    }

    /**
    * @var Receipt $receipt
    */
    public function map($receipt): array
    {
        $variations = implode(' - ', array_column($receipt->variations, 'value'));

        $state = $receipt->consignee->state;
        if (1 > strlen($state)) {
            $state = $receipt->consignee->city;
        }
        $remark = $receipt->receipt->remark;
        if ($remark) {
            $remark .= '-'; 
        }
        if ($receipt->receipt->buyer_msg) {
            $remark .= $receipt->receipt->buyer_msg;
        }

        return [
            $receipt->receipt_id,
            1 > strlen($receipt->etsy_sku) ? 'sku0': $receipt->etsy_sku,
            $variations,
            $receipt->quantity,
            $receipt->price,
            $receipt->receipt->currency_code,
            $receipt->consignee->name,
            $receipt->consignee->first_line,
            $receipt->consignee->second_line,
            $receipt->consignee->city,
            $state,
            $receipt->consignee->country_code,
            $receipt->consignee->zip,
            $receipt->consignee->phone,
            $receipt->consignee->phone,
            $receipt->buyer_email,
            '', // 税号
            '', // 门牌号
            '', // 公司名
            $remark,
            $receipt->image,
            '',
            '桌游用品',
            'Table Game',
            '1.98',
            '0.198',
            '', // 海关编码
            '', // 报关属性
            // Date::dateTimeToExcel($receipt->creation_tsz),
        ];
    }
}
