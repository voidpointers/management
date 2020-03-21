<?php

namespace Receipt\Entities;

use App\Model;

class Consignee extends Model
{
    protected $table = 'receipt_consignees';

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
        'phone',
    ];

    public function store(array $params)
    {
        $data = [];
        // 参数过滤
        foreach ($params as $key => $param) {
            foreach ($this->fillable as $fillable) {
                $data[$key][$fillable] = $param[$fillable] ?? '';
            }
            $data[$key]['receipt_id'] = $param['receipt_id'];
        }

        self::insert($data);
    }
}
