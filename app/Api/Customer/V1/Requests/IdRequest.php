<?php

namespace Api\Customer\V1\Requests;

use App\FormRequest;

class IdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'id' => 'required|int|min:1'
        ];
    }
}
