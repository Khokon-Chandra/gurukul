<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DashboardResource;
use App\Models\Department;
use App\Models\User;
use App\Trait\Authorizable;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    use Authorizable;

    public function index(): AnonymousResourceCollection
    {
        $data = Cache::remember('dashboard',30, function(){
            return Department::withCount('users')
            ->with('users')
            ->get();
        });

        return DashboardResource::collection($data);
    }
}
