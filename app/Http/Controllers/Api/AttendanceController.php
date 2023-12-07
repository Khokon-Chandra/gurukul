<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attendance\AttendanceRequest;
use App\Http\Resources\Api\AttendanceResource;
use App\Models\Attendance;
use App\Trait\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    use Authorizable;

    public function store(AttendanceRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $attendance = Attendance::create([
                'username' => $request->username,
                'amount' => $request->amount,
                'created_by' => Auth::user()->id
            ]);

            activity('Attendance Created')
                ->causedBy(Auth::user()->id)
                ->performedOn($attendance)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => "{$attendance->username}",
                    'activity' => 'Create Attendance',
                ])
                ->log("Attendance created successfully");

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'Attendance Saved!!',
                'data' => new AttendanceResource($attendance)
            ], 200);


        }catch (\Exception $error){
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function destroy(AttendanceRequest $request){
//        dd($request->all());
    }
}
