<?php

namespace App\Http\Requests\Api\Annoncement;

use App\Http\Requests\BaseFormRequest;
use Illuminate\Foundation\Http\FormRequest;

class AnnouncementRequest extends BaseFormRequest
{

    protected array $routeRequest = [
        'api/v1/announcements|post' => [
            'rules' => 'storeMethodRule',
        ],
        'api/v1/announcements|put' => [
            'rules' => 'updateMethodRule',
        ],
        'api/v1/update-announcement-status|patch' => [
            'rules' => 'announcementStatus',
        ],
        'api/v1/announcements|delete' => [
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
            'announcements' => ['required', 'array', 'min:1'],
            'announcements.*.id' => ['required', 'exists:announcements,id'],
            'announcements.*.message' => ['required', 'string', 'max:255'],
            'announcements.*.status' => ['required', 'boolean'],
        ];
    }

    public function announcementStatus(): void
    {
        $this->rules = [
            'announcement_id' => ['required']
        ];
    }

    public function destroyMethodRule(): void
    {
        $this->rules = [
            'announcements' => ['required', 'array', 'min:1'],
            'announcements.*' => ['exists:announcements,id']
        ];
    }
}
