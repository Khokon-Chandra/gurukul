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
use Illuminate\Support\Facades\Log;

class NotificationController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index(NotificationRequest $request): AnonymousResourceCollection
    {
        $data = Notification::with('createdBy')
            ->latest()
            ->filter($request)
            ->paginate(AppConstant::PAGINATION);

        return NotificationResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(NotificationRequest $request): JsonResponse
    {
        try {

            $notification = Notification::create($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Notification created successfully',
                'data'    => new NotificationResource($notification),
            ], 200);
        } catch (\Exception $error) {
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
    public function update(NotificationRequest $request, Notification $notification): JsonResponse
    {
        try {

            $notification->update($request->validated());

            return response()->json([
                'status'  => 'success',
                'message' => 'Notification updated successfully',
                'data'    => new NotificationResource($notification),
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(NotificationRequest $request, string $id): JsonResponse
    {
        try {
            Notification::whereIn('id', $request->ids)->delete();
            return response()->json([
                'status' => 'success',
                'message' => 'Notifications are deleted successfully'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}