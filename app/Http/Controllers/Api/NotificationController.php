<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Notification\NotificationRequest;
use App\Http\Resources\Api\Notification\NotificationResource;
use App\Models\Notification;
use App\Trait\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index(NotificationRequest $request): AnonymousResourceCollection
    {
        $data = Notification::with('createdBy')
            ->filter($request)
            ->latest()
            ->paginate(AppConstant::PAGINATION);

        return NotificationResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $notification = Notification::create($request->validated());

            activity('notification_created')->causedBy(Auth::id())
                ->performedOn($notification)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                    'target'   => $notification->name,
                    'activity' => 'Created notification',
                ])
                ->log('Created notification successfully');

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Notification created successfully',
                'data'    => new NotificationResource($notification),
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
     * Update the specified resource in storage.
     */
    public function update(NotificationRequest $request, Notification $notification): JsonResponse
    {
        DB::beginTransaction();
        try {

            $notification->update($request->validated());

            activity('notification_updated')->causedBy(Auth::id())
                ->performedOn($notification)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                    'target'   => $notification->name,
                    'activity' => 'updated notification',
                ])
                ->log('updated notification successfully');

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Notification updated successfully',
                'data'    => new NotificationResource($notification),
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
     * Update multiple
     */

    public function updateMultiple(NotificationRequest $request): JsonResponse
    {
        $attributes = $request->validated()['notifications'];

        DB::beginTransaction();
        try {

            $idArr = [];

            foreach ($attributes as $attribute) {

                $idArr[] = $attribute['id'];

                $notification = Notification::find($attribute['id']);

                $notification->update($attribute);

                activity('notification_updated')->causedBy(Auth::id())
                    ->performedOn($notification)
                    ->withProperties([
                        'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                        'target'   => $notification->name,
                        'activity' => 'updated notification',
                    ])
                    ->log('updated notification successfully');
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Multiple Notification updated successfully',
                'data'    => NotificationResource::collection(Notification::whereIn('id', $idArr)->get()),
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
    public function destroy(Notification $notification): JsonResponse
    {
        DB::beginTransaction();
        try {

            activity('notification deleted')->causedBy(Auth::id())
                ->performedOn($notification)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? request()->ip(),
                    'target'   => $notification->name,
                    'activity' => 'deleted notification',
                ])
                ->log('notification deleted successfully');

            $notification->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Notification deleted successfully'
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
     * Remove multiple resource from storage.
     */
    public function deleteMultiple(NotificationRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            foreach (Notification::whereIn('id', $request->notifications)->get() as $notification) :

                activity('notification deleted')->causedBy(Auth::id())
                    ->performedOn($notification)
                    ->withProperties([
                        'ip'       => Auth::user()->last_login_ip ?? request()->ip(),
                        'target'   => $notification->name,
                        'activity' => 'deleted notification',
                    ])
                    ->log('notification deleted successfully');

                $notification->delete();
            endforeach;

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Notifications are deleted successfully'
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
