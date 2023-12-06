<?php

namespace App\Http\Requests\Api\Chat;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/chats|post' => [
            'rules' => 'storeMethodRule',
        ],
    ];
    public function storeMethodRule(): void
    {
        $this->rules = [
            'send_to' => ['required', 'string', 'max:255'],
            'date' => ['required'],
            'time' => ['required'],
            'subject' => ['required', 'string']
        ];
    }
}
