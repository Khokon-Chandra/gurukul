<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Events\AnnouncementEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\Annoncement\AnnouncementRequest;
use App\Http\Resources\Api\ActivatedAnnouncementResource;
use App\Http\Resources\Api\AnnouncementResource;
use App\Models\Announcement;
use App\Trait\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AnnouncementController extends Controller
{

    use Authorizable;
    /**
     * Display a listing of the resource.
     */

    private array $updatedInstance = [];

    public function index(AnnouncementRequest $request): JsonResponse
    {

        $announcements  = Announcement::filter($request)->latest()->paginate(AppConstant::PAGINATION);

        return response()->json([
            'status' => 'success',
            'data' =>  AnnouncementResource::collection($announcements)
                ->response()
                ->getData(true)
        ], 200);
    }

    /**
     * Retrive already activated announcement
     */
    public function activated(): ActivatedAnnouncementResource
    {
        $announcement = Announcement::where('status', true)->first();
        return new ActivatedAnnouncementResource($announcement);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(AnnouncementRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $announcement = Announcement::create([
                'department_id' => $request->department_id,
                'message'       => $request->message,
                'status'        => $request->status,
                'created_by'    => Auth::id(),
            ]);

            if ($announcement->status) {
                Announcement::where('id', '!=', $announcement->id)->update([
                    'status' => false,
                ]);
            }

            AnnouncementEvent::dispatchIf($announcement->status, $announcement);


            activity("Announcement created")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement created successfully",
                    'target' => "{$announcement->message}",
                ])
                ->log(":causer.name created Announcement {$announcement->message}.");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Announcement Created!!',
                'data' => new AnnouncementResource($announcement), //use resource here
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    /**
     * Update Multiple Records.
     */
    public function update(AnnouncementRequest $request, Announcement $announcement): JsonResponse
    {
        DB::beginTransaction();
        try {

            $announcement->update($request->validated());

            AnnouncementEvent::dispatchIf($announcement->status, $announcement);

            if ($announcement->status) {
                Announcement::where('id', '!=', $announcement->id)->update([
                    'status' => false,
                ]);
            }

            activity("Announcement updated")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement updated successfully",
                    'target' => "{$announcement->message}",
                ])
                ->log(":causer.name updated Announcement {$announcement->message}.");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Announcement Updated!!',
                'data' => new AnnouncementResource($announcement) //use Resource
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }



    public function updateMultiple(AnnouncementRequest $request): JsonResponse
    {

        DB::beginTransaction();
        try {

            foreach ($request->announcements as $attribute) {
                $announcement = Announcement::find($attribute['id']);
                $announcement->update([
                    'department_id' => $attribute['department_id'],
                    'message'       => $attribute['message'],
                    'status'        => $attribute['status'],
                ]);

                $this->updatedInstance[] = $announcement;

                AnnouncementEvent::dispatchIf($announcement->status, $announcement);

                activity("Announcement updated")
                    ->causedBy(auth()->user())
                    ->performedOn($announcement)
                    ->withProperties([
                        'ip'       => Auth::user()->last_login_ip,
                        'activity' => "Announcement updated successfully",
                        'target'   => "{$announcement->message}",
                    ])
                    ->log(":causer.name updated Announcement {$announcement->message}.");
            }


            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully Announcement Updated!!',
                'data'    => AnnouncementResource::collection($this->updatedInstance) //use Resource
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function updateStatus(AnnouncementRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $announcement = Announcement::find($request->announcement_id);

            if ($announcement->status) {
                throw new \Exception('Please, activate another Announcement first. It will automatically become inactive', 422);
            }

            $announcement->update([
                'status' => true
            ]);


            activity("Announcement Status updated")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement status updated successfully. Action also updated the status of all other announcements",
                    'target' => "{$announcement->message}",
                ])
                ->log(":causer.name updated Announcement {$announcement->message}.");


            Announcement::where('id', '!=', $announcement->id)
                ->where('status', true)
                ->update([
                    'status' => false
                ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Announcement status Updated Successfully',
                'data' => AnnouncementResource::collection(Announcement::paginate(AppConstant::PAGINATION))->response()->getData(true)
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            Log::error($error);
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], $error->getCode());
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Announcement $announcement): JsonResponse
    {
        DB::beginTransaction();
        try {

            activity("Announcement deleted")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip,
                    'activity' => "Announcement deleted successfully",
                    'target'   => "{$announcement->message}"
                ])
                ->log(":causer.name deleted multiple Announcements {$announcement->message}.");

            $announcement->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Announcements Deleted Successfully',
            ], 200);
        } catch (\Exception $error) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }


    /**
     * Remove the multiple resource from storage.
     */
    public function deleteMultiple(AnnouncementRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $announcements = Announcement::whereIn('id', $request->announcements)
                ->get();

            foreach ($announcements as $announcement) {
                activity("Announcement deleted")
                    ->causedBy(auth()->user())
                    ->performedOn($announcement)
                    ->withProperties([
                        'ip' => Auth::user()->last_login_ip,
                        'activity' => "Announcement deleted successfully",

                        'target' => "{$announcement->message}"

                    ])
                    ->log(":causer.name deleted multiple Announcements {$announcement->message}.");

                $announcement->delete();
            }


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Announcements Deleted Successfully',
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }


    public function getData(Request $request): JsonResponse
    {
        $announcement = Announcement::where('status', true)->firstOrFail();
        return response()->json([
            'status' => 'success',
            'data' =>  new AnnouncementResource($announcement)
        ], 200);
    }
}
