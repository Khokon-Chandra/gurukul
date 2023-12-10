<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\AttendanceRequest;
use App\Http\Resources\Api\AttendanceResource;
use App\Models\Attendance;
use App\Trait\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index(AttendanceRequest $request): AnonymousResourceCollection
    {
        $data = Attendance::with('createdBy')
            ->filter($request)
            ->latest()
            ->paginate(AppConstant::PAGINATION);

        return AttendanceResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AttendanceRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $attendance = Attendance::create($request->validated());

            activity('attendance_created')->causedBy(Auth::id())
                ->performedOn($attendance)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                    'target'   => $attendance->name,
                    'activity' => 'Created attendance',
                ])
                ->log('Created attendance successfully');

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Attendance created successfully',
                'data'    => new AttendanceResource($attendance),
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(AttendanceRequest $request, Attendance $attendance): JsonResponse
    {
        DB::beginTransaction();
        try {

            $attendance->update($request->validated());

            activity('attendance_updated')->causedBy(Auth::id())
                ->performedOn($attendance)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                    'target'   => $attendance->name,
                    'activity' => 'updated attendance',
                ])
                ->log('updated attendance successfully');

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Attendance updated successfully',
                'data'    => new AttendanceResource($attendance),
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update multiple 
     */

    public function updateMultiple(AttendanceRequest $request): JsonResponse
    {
        $attributes = $request->validated()['attendances'];

        DB::beginTransaction();
        try {

            $idArr = [];

            foreach ($attributes as $attribute) {

                $idArr[] = $attribute['id'];

                $attendance = Attendance::find($attribute['id']);

                $attendance->update($attribute);

                activity('attendance_updated')->causedBy(Auth::id())
                    ->performedOn($attendance)
                    ->withProperties([
                        'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                        'target'   => $attendance->name,
                        'activity' => 'updated attendance',
                    ])
                    ->log('updated attendance successfully');
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Multiple Attendance updated successfully',
                'data'    => AttendanceResource::collection(Attendance::whereIn('id', $idArr)->get()),
            ], 200);
        } catch (\Exception $error) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Attendance $attendance): JsonResponse
    {
        try {

            $attendance->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendances are deleted successfully'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove multiple resource from storage.
     */
    public function deleteMultiple(AttendanceRequest $request): JsonResponse
    {
        try {

            Attendance::whereIn('id', $request->attendances)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Attendances are deleted successfully'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
