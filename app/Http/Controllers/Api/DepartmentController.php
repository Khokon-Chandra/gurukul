<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\DepartmentResource;
use App\Models\Department;
use App\Trait\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class DepartmentController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $departments = Department::when($request->name ?? false, fn($query, $name) => $query
                        ->where('name','like',"%$name%"))
                        ->get();
        return DepartmentResource::collection($departments);
    }

}
