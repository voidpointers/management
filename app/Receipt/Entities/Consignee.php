<?php

namespace Receipt\Entities;

use App\Model;

class Consignee extends Model
{
    protected $table = 'receipt_consignees';

    protected $guarded = ['receipt_sn'];

    protected $fillable = [
        'country_id',
        'country_code',
        'country',
        'name',
        'state',
        'city',
        'zip',
        'first_line',
        'second_line',
        'formatted_address',
        'phone',
        'update_time',
    ];

    public function store(array $params)
    {
        $data = [];
        // 参数过滤
        foreach ($params as $key => $param) {
            foreach ($this->fillable as $fillable) {
                if ($value = $param[$fillable] ?? '') {
                    $data[$key][$fillable] = $value;
                }
            }
            $data['receipt_id'] = $param['receipt_id'];
        }

        self::insert($data);
    }
}
