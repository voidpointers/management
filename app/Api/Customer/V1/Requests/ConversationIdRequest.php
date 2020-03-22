<?php

namespace Api\Message\V1\Requests;

use App\FormRequest;

class ConversationIdRequest extends FormRequest
{
    public function rules()
    {
        return [
            'conversation_id' => 'required|int|min:1'
        ];
    }
}
