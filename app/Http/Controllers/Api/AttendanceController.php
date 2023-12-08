<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Attendance\AttendanceRequest;
use App\Http\Resources\Api\AnnouncementResource;
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


    public function index(Request $request): JsonResponse
    {

        $query = Attendance::query();


        if($request->filled('search')){
            $query->where('username', 'LIKE', "{$request->search}");
        }


        if($request->filled('username')){
            $query->orderBy('username', $request->username);
        }


        if($request->filled('amount')){
            $query->orderBy('amount', $request->amount);
        }

        $attendances = $query->latest()->paginate(AppConstant::PAGINATION);


        return response()->json([
            'status' => 'success',
            'data' =>  AttendanceResource::collection($attendances)->response()->getData(true)

        ], 200);
    }
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


        } catch (\Exception $error) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function destroy(AttendanceRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $attendances = Attendance::whereIn('id', $request->attendances)->get();

            foreach ($attendances as $attendance) {

                activity("Attendance Deleted")
                    ->causedBy(auth()->user())
                    ->performedOn($attendance)
                    ->withProperties([
                        'ip' => Auth::user()->last_login_ip,
                        'activity' => "Attendance deleted successfully",
                        'target' => "{$attendance->username}",
                    ])
                    ->log("Attendance Deleted");

                $attendance->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Announcement Deleted',
            ], 200);

        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function update(AttendanceRequest $request)
    {
        $attendanceId = $request->attendance['id'];
        DB::beginTransaction();
        try {

            $attendance = Attendance::find($attendanceId);

            $attendance->update([
                'username' => $request->attendance['username'],
                'amount' => $request->attendance['amount'],
                'created_by' => Auth::user()->id
            ]);

            activity("Attendance updated")
                ->causedBy(auth()->user())
                ->performedOn($attendance)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Attendance updated successfully",
                    'target' => "{$attendance->username}",
                ])
                ->log("Attendance Updated");


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Attendance Updated!!',
                'data' => new AttendanceResource($attendance)
            ], 200);

        } catch (\Exception $error) {
            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }
}
