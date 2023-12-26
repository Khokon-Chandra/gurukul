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
        'api/v1/user-update/{user}|put' => [
            'rules' => 'updateMethodRule',
        ],
        'api/v1/delete-user|delete' => [
            'rules' => 'deleteMethodRule',
        ],
        'api/v1/change-password/{user}|put' => [
            'rules' => 'passwordUpdateMethodRule',
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
            'name'     => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users','username')->ignore($this->route('user'))
            ],
            'role' => 'required|exists:roles,id'
        ];
    }

    public function deleteMethodRule(): void
    {
        $this->rules = [
            'ids.*'     => 'required|exists:users,id',
        ];
    }

    public function passwordUpdateMethodRule(): void {
        $this->rules = [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }
}
