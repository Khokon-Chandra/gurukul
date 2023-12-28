<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AttendanceRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/attendances|get'  => [
            'rules'                => 'indexMethodRule',
            'prepareForValidation' => 'indexPrepareForValidation',
        ],
        'api/v1/attendances|post'  => [
            'rules'                => 'storeMethodRule',
        ],
        'api/v1/attendances/{attendance}|put'   => [
            'rules'                => 'updateMethodRule',
        ],
        'api/v1/attendances|patch' => [
            'rules'                => 'updateMultipleMethodRule',
        ],
        'api/v1/attendances-delete-many|delete' => [
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
                Rule::in(['name', 'amount', 'created_at','department_id']),
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
            'name'       => 'required|min:1|max:255|string',
            'amount'     => 'required|numeric|decimal:0,8',
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
            'attendances'          => 'required|array|min:1',
            'attendances.*.id'     => 'required|exists:attendances,id',
            'attendances.*.name'   => 'required|min:1|max:255|string',
            'attendances.*.amount' => 'required|numeric|decimal:0,8',
            'attendances.*.department_id' => 'required|exists:departments,id',
        ];
    }


    public function deleteMethodRule(): void
    {
        $this->rules = [
            'attendances' => [
                'required',
                'array',
                'min:1'
            ],
            'attendances.*' => 'exists:attendances,id'
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
