<?php

namespace Api\Order\V1\Requests;

use App\FormRequest;

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
