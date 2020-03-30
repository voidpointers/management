<?php

namespace Api\Order\V1\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Order\Entities\Logistics;
use Order\Entities\Receipt;

// class ReceiptImport implements ToCollection, WithHeadingRow
class ReceiptImport implements ToCollection, WithStartRow
{
    public function collection(Collection $rows)
    {
        $receipt_ids = $rows->map(function ($row) {
            return $row[0];
        });

        // 获取Receipts
        $receipts = Receipt::whereIn('receipt_id', $receipt_ids)
        ->whereIn('status', [1, 2])
        ->get(['receipt_sn'])
        ->pluck('receipt_sn', 'receipt_id')
        ->all();
        if (!$receipts) {
            throw new \RuntimeException('订单不存在');
        }

        $cur_time = time();

        $data = [];
        foreach ($rows as $row) {
            $provider = explode('-', $row[1]);

            $data[] = [
                'tracking_code' => $row[2],
                'receipt_sn' => $receipts[$row[0]] ?? 0,
                'provider' => json_encode([
                    'provider' => $provider[0] ?? '',
                    'channel' => $provider[1] ?? ''
                ]),
                'provider_id' => 1,
                'status' => 1,
                'create_time' => $cur_time,
                'update_time' => $cur_time
            ];
        }

        Receipt::whereIn('receipt_sn', $receipts)->update([
            'status' => 8,
            'complete_time' => $cur_time,
            'dispatch_time' => $cur_time
        ]);

        (new Logistics)->store($data);
    }

    public function startRow(): int
    {
        return 2;
    }
}
