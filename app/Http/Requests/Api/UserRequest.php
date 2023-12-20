<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseFormRequest
{

    protected array $routeRequest = [
        'api/v1/create-user|post' => [
            'rules' => 'storeMethodRule',
        ],
        'api/v1/user/{user}|put' => [
            'rules' => 'updateMethodRule',
        ],
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
            'department_id' => 'required|exists:departments,id',
            'name'     => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users','username')
            ],

            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users','email')
            ],
            'password'      => 'required|string|min:8|confirmed',
            'role' => 'required|exists:roles,id',
            'timezone' => 'nullable|string|max:100',
        ];
    }


    public function updateMethodRule(): void
    {
        $this->rules = [
            'department_id' => 'required|exists:departments,id',
            'name'     => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users','username')->ignore($this->route('user'))
            ],

            'email' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users','email')->ignore($this->route('user'))
            ],

            'role' => 'required|exists:roles,id',
            'timezone' => 'nullable|string|max:100',
        ];
    }
}
