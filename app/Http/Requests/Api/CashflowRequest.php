<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class CashflowRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/cashflows|get'  => [
            'rules'                => 'indexMethodRule',
            'prepareForValidation' => 'indexPrepareForValidation',
        ],
        'api/v1/cashflows|post'  => [
            'rules'                => 'storeMethodRule',
        ],
        'api/v1/cashflows/{cashflow}|put'   => [
            'rules'                => 'updateMethodRule',
        ],
        'api/v1/cashflows|patch' => [
            'rules'                => 'updateMultipleMethodRule',
        ],
        'api/v1/cashflows-delete-many|delete' => [
            'rules'                => 'deleteMethodRule',
        ],
    ];


    public function indexMethodRule(): void
    {
        $this->rules = [
            'department_id'         => 'nullable',
            'name'                  => 'nullable',
            'amount'                => 'nullable|numeric',
            'from_date'             => 'nullable|date',
            'to_date'               => 'nullable|date',
            'date_range'            => 'nullable|string|max:50',
            'sort_by'               => [
                'nullable',
                Rule::in(['department_id','name', 'amount', 'created_at']),
            ],
            'sort_type'             => [
                'nullable',
                Rule::in(['ASC', 'DESC']),
            ],
            'searchable_date_range' => 'nullable'
        ];
    }

    public function storeMethodRule(): void
    {
        $this->rules = [
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|min:1|max:255|string',
            'amount'        => 'required|numeric|decimal:0,8',
        ];
    }

    public function updateMethodRule(): void
    {
        $this->rules = [
            'department_id' => 'required|exists:departments,id',
            'name'          => 'required|min:1|max:255|string',
            'amount'        => 'required|numeric|decimal:0,8',
        ];
    }

    public function updateMultipleMethodRule(): void
    {
        $this->rules = [
            'cashflows'                 => 'required|array|min:1',
            'cashflows.*.id'            => 'required|exists:cashflows,id',
            'cashflows.*.department_id' => 'required|exists:departments,id',
            'cashflows.*.name'          => 'required|min:1|max:255|string',
            'cashflows.*.amount'        => 'required|numeric|decimal:0,8',
        ];
    }


    public function deleteMethodRule(): void
    {
        $this->rules = [
            'cashflows' => [
                'required',
                'array',
                'min:1'
            ],
            'cashflows.*' => 'exists:cashflows,id'
        ];
    }


    public function indexPrepareForValidation(): void
    {
        $dateRange = null;
        $dates     = explode(' to ', $this->date_range) ?? false;

        if ($this->from_date && $this->to_date) {
            $dateRange = [
                Carbon::parse($this->from_date)->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($this->to_date)->endOfDay()->format('Y-m-d H:i:s'),
            ];
        }

        if ($this->date_range && $dates) {

            $dateRange = [
                Carbon::parse($dates[0])->startOfDay()->format('Y-m-d H:i:s'),
                Carbon::parse($dates[1])->endOfDay()->format('Y-m-d H:i:s'),
            ];
        }


        $this->prepareForValidationRules = [
            'sort_type' => $this->sort_type ?? 'ASC',
            'searchable_date_range' => $dateRange
        ];
    }
}
