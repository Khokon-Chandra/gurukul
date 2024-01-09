<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RoleRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/roles|post' => [
            'rules' => 'storeMethodRule',
        ],
        'api/v1/roles/{role}|put' => [
            'rules' => 'updateMethodRule',
        ],
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
            'name' => ['required', 'string', 'unique:roles,name'],
            'department_id' => ['required', 'exists:departments,id'],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ];
    }

    public function updateMethodRule(): void{
        $this->rules = [
           'name' => [
               'required',
               'string',
             Rule::unique('roles')->ignore($this->route('role'))
           ],
            'permissions' => ['required', 'array'],
            'permissions.*' => ['exists:permissions,id']
        ];
    }
}

