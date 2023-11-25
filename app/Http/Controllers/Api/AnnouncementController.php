<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Events\AnnouncementEvent;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UpdateAnnouncementRequest;
use App\Http\Resources\Api\AnnouncementResource;
use App\Models\Announcement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    private array $updatedInstance = [];

    public function index(Request $request)
    {

        $announcements  = Announcement::latest()->filter($request)->paginate(20);

        return response()->json([
            'status' => 'success',
            'data' =>  AnnouncementResource::collection($announcements)->response()->getData(true)
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'message' => ["required", "string", "max:255"],
            'status' => ["required", "boolean"]
        ]);

        DB::beginTransaction();
        try {

            $announcement = Announcement::create([
                'number' => Announcement::max('id') + 1,
                'message' => $request->message,
                'status' => $request->status,
                'created_by' => Auth::id(),
            ]);

            AnnouncementEvent::dispatchIf($announcement->status, $announcement);


            activity("Announcement created")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement created successfully",
                    'target' => "$announcement->message",
                ])
                ->log(":causer.name created Announcement $announcement->message.");

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
    public function update(UpdateAnnouncementRequest $request)
    {

        DB::beginTransaction();
        try {

            foreach ($request->announcements as $attribute) {
                $announcement = Announcement::find($attribute['id']);
                $announcement->update([
                    'message' => $attribute['message'],
                    'status' => $attribute['status'],
                ]);

                $this->updatedInstance[] = $announcement;

                AnnouncementEvent::dispatchIf($announcement->status, $announcement);

                activity("Announcement updated")
                    ->causedBy(auth()->user())
                    ->performedOn($announcement)
                    ->withProperties([
                        'ip' => Auth::user()->last_login_ip,
                        'activity' => "Announcement updated successfully",
                        'target' => "$announcement->message",
                    ])
                    ->log(":causer.name updated Announcement $announcement->message.");
            }


            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Successfully Announcement Updated!!',
                'data' => AnnouncementResource::collection($this->updatedInstance) //use Resource
            ], 200);

        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage(),
            ], 500);
        }
    }

    public function updateAnAnnouncementStatus(Request $request): JsonResponse
    {

        $request->validate([
            'announcement_id' => ['required']
        ]);


        DB::beginTransaction();


        try {

            $announcement = Announcement::find($request->announcement_id);
            $announcement->update([
                'status' => !$announcement->status
            ]);


            activity("Announcement Status updated")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement status updated successfully. Action also updated the status of all other announcements",
                    'target' => "$announcement->message",
                ])
                ->log(":causer.name updated Announcement $announcement->message.");


            $allOtherAnnouncements = Announcement::where('id', '!=', $announcement->id)->get();

            foreach ($allOtherAnnouncements as $otherAnnouncement) {
                $otherAnnouncement->update([
                    'status' => false
                ]);
            }


            DB::commit();


            return response()->json([
                'status' => 'success',
                'message' => 'Announcement status Updated Successfully',
                'data' => AnnouncementResource::collection(Announcement::paginate(AppConstant::PAGINATION))->response()->getData(true)
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
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request)
    {
        $request->validate([
            'announcements' => ['required', 'array', 'min:1'],
            'announcements.*' => ['exists:announcements,id']
        ]);

        DB::beginTransaction();
        try {

            $announcements = Announcement::whereIn('id', $request->announcements)->get();

            foreach($announcements as $announcement){
                $announcement->delete();
            }

            activity("Announcement deleted")
                ->causedBy(auth()->user())
                ->performedOn($announcement)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Announcement deleted successfully",
                    'target' => "$announcement->message",
                ])
                ->log(":causer.name deleted multiple Announcements $announcement->message.");



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


}
