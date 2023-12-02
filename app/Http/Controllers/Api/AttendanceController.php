<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\AttendanceResource;
use App\Models\Attendance;
use App\Trait\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AttendanceController extends Controller
{
    use Authorizable;

    public function index(Request $request): AnonymousResourceCollection
    {
        $attendance = Attendance::with('createdBy')->latest()
        ->filter($request)
        ->paginate(AppConstant::PAGINATION);

        return AttendanceResource::collection($attendance);
    }
}
