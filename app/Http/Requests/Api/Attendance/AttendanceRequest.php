<?php

namespace App\Http\Requests\Api\Attendance;

use App\Http\Requests\BaseFormRequest;


class AttendanceRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/create-attendance|post' => [
            'rules' => 'storeMethodRule',
        ],
        'api/v1/delete-attendance|delete' => [
            'rules' => 'destroyMethodRule',
        ],
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
            'username' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer'],
        ];
    }

    public function destroyMethodRule(): void
    {
        $this->rules = [
            'attendances' => ['required', 'array', 'min:1'],
            'attendances.*' => ['exists:attendances,id']
        ];
    }



}
