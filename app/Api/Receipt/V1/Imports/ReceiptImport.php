<?php

namespace Api\Receipt\V1\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Package\Entities\Logistics;
use Package\Entities\Package;
use Receipt\Entities\Receipt;

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
        ->get()
        ->pluck('package_sn', 'receipt_id', 'receit_sn');
        if ($receipts->isEmpty()) {
            throw new \RuntimeException('订单不存在');
        }

        // 过滤已导入物流
        $logistics = Logistics::whereIn('package_sn', $receipts)
        ->get(['package_sn'])
        ->pluck('package_sn');

        $cur_time = time();

        $data = [];
        foreach ($rows as $row) {
            $package_sn = $receipts[$row[0]];
            if (!$package_sn) {
                continue;
            }
            if (in_array($package_sn, $logistics->toArray())) {
                continue;
            }

            $provider = explode('-', $row[1]);

            $data[] = [
                'type' => 2,
                'tracking_code' => $row[2],
                'package_sn' => $package_sn,
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
        if (!$data) {
            throw new \RuntimeException('包裹不存在');
            return [];
        }

        Package::whereIn('package_sn', $receipts)->update([
            'status' => 8,
            'print_time' => $cur_time,
            'track_time' => $cur_time,
            'complete_time' => $cur_time,
            'dispatch_time' => $cur_time,
        ]);

        Receipt::whereIn('package_sn', $receipts)->update([
            'status' => 8,
            'complete_time' => $cur_time,
            'dispatch_time' => $cur_time
        ]);

        Logistics::insert($data);
    }

    public function startRow(): int
    {
        return 2;
    }
}
