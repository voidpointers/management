<?php

namespace Api\Message\V1\Requests;

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
