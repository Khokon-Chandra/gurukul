<?php

namespace App\Http\Requests\Api\UserIP;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\Rule;

class UserIpRequest extends BaseFormRequest
{
    protected array $routeRequest = [

        'api/v1/ip|get' => [
            'rules' => 'indexMethod',
            'prepareForValidation' => 'indexPrepareForSearch',
        ],  

        'api/v1/ip|post' => [
            'rules' => 'storeMethodRule',
        ],

        'api/v1/ip/{ip}|put' => [
            'rules' => 'updateMethodRule',
        ],

        'api/v1/ip/{ip}|patch' => [
            'rules' => 'updateMethodRule',
        ],

        'api/v1/user-ips|put' => [
            'rules' => 'multipleUpdateMethodRule',
        ],

        'api/v1/user-ip-delete-multiple|delete' => [
            'rules' => 'deleteMethodRule',
        ]
    ];

    public function indexMethod(): void
    {
        $this->rules = [
            'ip' => 'nullable|string|max:255',
            'sort_by'               => [
                'nullable',
                Rule::in(['ip', 'description', 'status','whitelisted','date','created_at','updated_at']),
            ],
            'sort_type'             => [
                'nullable',
                Rule::in(['ASC', 'DESC']),
            ],
        ];
    }

    public function storeMethodRule(): void
    {
        $this->rules = [
            'number1' => 'required|min:1|max:255|numeric',
            'number2' => 'required|min:0|max:255|numeric',
            'number3' => 'required|min:0|max:255|numeric',
            'number4' => 'required|min:0|max:255|numeric',
            'description' => 'required',
        ];
    }

    public function updateMethodRule(): void
    {
        $this->rules = [
            'number1' => 'required|min:1|max:255|numeric',
            'number2' => 'required|min:0|max:255|numeric',
            'number3' => 'required|min:0|max:255|numeric',
            'number4' => 'required|min:0|max:255|numeric',
            'whitelisted' => 'required|boolean',
            'description' => 'required|max:255',
        ];
    }

    public function multipleUpdateMethodRule(): void
    {
        $this->rules = [
            'items' => 'required|array',
            'items.*.id' => 'required|integer',
            'items.*.item' => 'required|array',
            'items.*.item.number1' => 'required|integer',
            'items.*.item.number2' => 'required|integer',
            'items.*.item.number3' => 'required|integer',
            'items.*.item.number4' => 'required|integer',
            'items.*.item.whitelisted' => 'required|boolean',
            'items.*.item.description' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'number1.required' => 'The number 1 IP is required',
            'number2.required' => 'The number 2 IP is required',
            'number3.required' => 'The number 3 IP is required',
            'number4.required' => 'The number 4 IP is required',
            '*.min' => 'The number must be at least 0.',
            '*.max' => 'The number must not be greater than 255',

            'whitelisted.required' => 'The number whitelisted is required',
            'description.required' => 'The number description is required',

            'items.required' => 'The items field is required.',
            'items.array' => 'The items must be an array.',
            'items.*.id.required' => 'The ID is required for item :itemIndex.',
            'items.*.id.integer' => 'The ID for item :itemIndex must be an integer.',
            'items.*.item.required' => 'The item for item :itemIndex is required.',
            'items.*.item.array' => 'The item for item :itemIndex must be an array.',
            'items.*.item.number1.required' => 'The number1 field for item :itemIndex is required.',
            'items.*.item.number1.integer' => 'The number1 field for item :itemIndex must be an integer.',
            'items.*.item.number2.required' => 'The number2 field for item :itemIndex is required.',
            'items.*.item.number2.integer' => 'The number2 field for item :itemIndex must be an integer.',
            'items.*.item.number3.required' => 'The number3 field for item :itemIndex is required.',
            'items.*.item.number3.integer' => 'The number3 field for item :itemIndex must be an integer.',
            'items.*.item.number4.required' => 'The number4 field for item :itemIndex is required.',
            'items.*.item.number4.integer' => 'The number4 field for item :itemIndex must be an integer.',
            'items.*.item.whitelisted.required' => 'The whitelisted field is required.',
            'items.*.item.whitelisted.integer' => 'The whitelisted field must be an integer.',
            'items.*.item.description.required' => 'The description field for item :itemIndex is required.',
            'items.*.item.description.string' => 'The description field for item :itemIndex must be a string.',
            'items.*.item.description.max' => 'The description field for item :itemIndex may not be greater than :max characters.',
        ];
    }


    public function deleteMethodRule(): void
    {
        $this->rules = [
            'items' => 'required|array|min:1',
            'items.*' => 'exists:user_ips,id',
        ];
    }

    public function indexPrepareForSearch(): void
    {
        
        $this->prepareForValidationRules = [
            'ip' => $this->ip,
            'sort_by' => $this->sort_by == 'status' ? 'whitelisted' : ($this->sort_by == 'date' ? 'updated_at' : $this->sort_by),
        ];
    }


    /**
     * todo set custom names for validation with wildcats
     *  e.g
     *  $validator->setAttributeNames([
     *      'items.*.id' => 'ID',
     *      'items.*.item.number1' => 'Number 1',
     *      'items.*.item.number2' => 'Number 2',
     *      'items.*.item.number3' => 'Number 3',
     *      'items.*.item.number4' => 'Number 4',
     *      'items.*.item.whitelisted' => 'Whitelisted',
     *      'items.*.item.description' => 'Description',
     *  ]);
     */
}
