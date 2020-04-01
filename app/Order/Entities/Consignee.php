<?php

namespace Order\Entities;

use App\Model;

class Consignee extends Model
{
    protected $table = 'receipt_consignees';

    protected $fillable = [
        'receipt_sn',
        'receipt_id',
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

    public function store(array $params, $uk = 'receipt_id')
    {
        $data = array_map(function ($item) {
            if (!$item['second_line']) {
                $item['second_line'] = '';
            }
            return $item;
        }, $params);
        
        return parent::store($data, $uk);
    }
}
