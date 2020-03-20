<?php

namespace Api\Receipt\V1\Requests;

use Api\FormRequest;

class ConsigneeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'country_id' => 'integer',
            'name' => '',
            'state' => '',
            'city' => '',
            'zip' => '',
            'first_line' => '',
            'second_line' => '',
            'formatted_address' => '',
            'phone' => '',
        ];
    }

    public function messages()
    {
        return [
            'country_id.integer' => '国家ID必须是数字'
        ];
    }
}
