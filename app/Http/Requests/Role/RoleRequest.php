<?php

namespace App\Http\Requests\Role;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class RoleRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/roles|post' => [
            'rules' => 'storeMethodRule',
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
}
