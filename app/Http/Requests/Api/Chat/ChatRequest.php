<?php

namespace App\Http\Requests\Api\Chat;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class ChatRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/groups/{group}|post' => [
            'rules' => 'storeMethodRule',
        ],
    ];
    public function storeMethodRule(): void
    {
        $this->rules = [
            'message' => ['required', 'string']
        ];
    }
}
