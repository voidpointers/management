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
}
