<?php

namespace Api\Receipt\V1\Requests;

use Api\FormRequest;

class ReceiptRequest extends FormRequest
{
    public function rules()
    {
        return [
            'remark' => 'string',
            'logistics_speed' => 'integer',
        ];
    }
}
