<?php

namespace Api;

use Dingo\Api\Http\FormRequest as BaseFormRequest;

class FormRequest extends BaseFormRequest
{
    public function authorize()
    {
        return true;
    }

    public function validated()
    {
        return $this->getValidatorInstance()->validated();
    }
}
