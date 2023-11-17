<?php

namespace App\Http\Requests\Api\Notification;

use App\Http\Requests\BaseFormRequest;
use Carbon\Carbon;

class NotificationRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/notifications|post'  => [
            'rules'                => 'indexMethodRule',
            'prepareForValidation' => 'indexPrepareForValidation',
        ],
        'api/v1/notifications|post'  => [
            'rules'                => 'storeMethodRule',
        ],
        'api/v1/notifications/{notification}|put'   => [
            'rules'                => 'updateMethodRule',
        ],
        'api/v1/notifications/{notification}|patch' => [
            'rules'                => 'updateMethodRule',
        ],
        'api/v1/notifications/{notification}|delete' => [
            'rules'                => 'deleteMethodRule',
            'prepareForValidation' => 'deletePrepareForValidation',
        ],
    ];


    public function indexMethodRule(): void
    {
        $this->rules = [
            'subject'               => 'nullable',
            'from_date'             => 'nullable|date',
            'to_date'               => 'nullable|date',
            'date_range'            => 'nullable|string|max:50',
            'searchable_date_range' => 'nullable'
        ];
    }

    public function storeMethodRule(): void
    {
        $this->rules = [
            'subject' => 'required|min:1|max:255|string',
            'date'    => 'required|date|date_format:Y-m-d',
            'time'    => 'required|date_format:H:i',
        ];
    }

    public function updateMethodRule(): void
    {
        $this->rules = [
            'subject' => 'required|min:1|max:255|string',
            'date'    => 'required|date|date_format:Y-m-d',
            'time'    => 'required|date_format:H:i',
        ];
    }


    public function deleteMethodRule(): void
    {
        $this->rules = [
            'ids' => [
                'required',
                'array',
                'min:1'
            ],
            'ids.*' => 'exists:notifications,id'
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
            'searchable_date_range' => $dateRange
        ];
    }


    public function deletePrepareForValidation(): void
    {
        $idString = $this->route('notification');
        $idArray  = explode(',',$idString);

        if(is_array($idArray)){
            $idArray = array_map(fn($id) => trim($id),$idArray);
        }

        $this->prepareForValidationRules = [
            'ids' => $idArray
        ];
    }
}
