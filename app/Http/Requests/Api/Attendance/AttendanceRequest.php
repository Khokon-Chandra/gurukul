<?php

namespace App\Http\Requests\Api\Attendance;

use App\Http\Requests\BaseFormRequest;


class AttendanceRequest extends BaseFormRequest
{
    protected array $routeRequest = [
        'api/v1/create-attendance|post' => [
            'rules' => 'storeMethodRule',
        ],
    ];

    public function storeMethodRule(): void
    {
        $this->rules = [
            'username' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'integer'],
        ];
    }

}
