<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserRequest extends BaseFormRequest
{
    
    protected array $routeRequest = [
        'api/v1/user/{user}|put' => [
            'rules' => 'storeMethodRule',
            // 'prepareForValidation' => 'storePrepareForValidation'
        ],
       
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
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
