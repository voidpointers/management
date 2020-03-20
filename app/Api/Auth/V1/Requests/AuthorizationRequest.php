<?php

namespace Api\Auth\V1\Requests;

use App\FormRequest;

class AuthorizationRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'username' => 'required|string',
            'password' => 'required|string|min:4',
        ];
    }

    public function messages()
    {
        return [
            'username.required' => '用户名不能为空。',
            'password.required' => '密码不能为空。',
        ];
    }
}
