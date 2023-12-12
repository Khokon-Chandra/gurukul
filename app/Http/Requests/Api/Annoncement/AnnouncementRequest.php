<?php

namespace App\Http\Requests\Api\Annoncement;

use App\Http\Requests\BaseFormRequest;
use App\Rules\Announcement\MultipleActiveStatusNotAllow;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class AnnouncementRequest extends BaseFormRequest
{

    protected array $routeRequest = [

        'api/v1/announcements|get' => [
            'rules' => 'indexMethodRule',
            'prepareForValidation' => 'indexPrepareForValidation',
        ],

        'api/v1/announcements|post' => [
            'rules' => 'storeMethodRule',
        ],

        'api/v1/announcements/{announcement}|put' => [
            'rules' => 'updateMethodRule',
        ],

        'api/v1/announcements-update-multiple|put' => [
            'rules' => 'updateMultipleMethodRule',
        ],

        'api/v1/update-announcement-status|patch' => [
            'rules' => 'announcementStatus',
        ],
        'api/v1/announcements-delete-multiple|delete' => [
            'rules' => 'destroyMethodRule',
        ],
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
            'message' => ["required", "string", "max:255"],
            'status' => ["required", "boolean"],
        ];
    }

    public function updateMethodRule(): void
    {
        $this->rules = [
            'message' => 'required|max:255',
            'status'  => 'required|boolean'
        ];
    }


    public function updateMultipleMethodRule(): void
    {
        $this->rules = [
            'announcements' => ['required', 'array', 'min:1'],
            'announcements.*.id' => ['required', 'exists:announcements,id'],
            'announcements.*.message' => ['required', 'string', 'max:255'],
            'announcements.*.status' => ['required', 'boolean',new MultipleActiveStatusNotAllow($this->announcements)],
        ];
    }


    public function announcementStatus(): void
    {
        $this->rules = [
            'announcement_id' => 'required|exists:announcements,id'
        ];
    }

    public function destroyMethodRule(): void
    {
        $this->rules = [
            'announcements' => ['required', 'array', 'min:1'],
            'announcements.*' => ['exists:announcements,id']
        ];
    }


    public function indexMethodRule(): void
    {
        $this->rules = [
            'message'               => 'nullable',
            'status'                => 'nullable|numeric',
            'from_date'             => 'nullable|date',
            'to_date'               => 'nullable|date',
            'date_range'            => 'nullable|string|max:50',
            'sort_by'               => [
                'nullable',
                Rule::in(['message', 'status', 'created_at']),
            ],
            'sort_type'             => [
                'nullable',
                Rule::in(['ASC', 'DESC']),
            ],
            'searchable_date_range' => 'nullable'
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
