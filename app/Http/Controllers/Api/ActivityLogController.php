<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ActivityExportableResource;
use App\Http\Resources\Api\ActivityResource;
use App\Trait\Authorizable;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Spatie\Activitylog\Models\Activity;

class ActivityLogController extends Controller
{
    use Authorizable;
    /**
     * Handle the incoming request.
     */
    public function index(Request $request): AnonymousResourceCollection
    {

        $query = Activity::with('subject.department','causer');

        $data = $this->filter($query, $request)
            
            ->paginate(AppConstant::PAGINATION);

        return ActivityResource::collection($data);
    }



    public function download(Request $request): AnonymousResourceCollection
    {

        $query = Activity::with('subject.department','causer');

        $data  = $this->filter($query, $request)
            ->get()->map(function ($item, $index) {
                $item->no = $index + 1;
                return $item;
            });

        return ActivityExportableResource::collection($data);
    }



    private function filter($query, $request)
    {
        $dateRange = $request->dateRange ? explode('to', $request->dateRange) : false;

        return $query
            ->when($request->description ?? false, function ($query, $description) {
                $query->where('description', 'like', "%{$description}%");
            })
            ->when($request->department_id, function ($query, $departmentId) {

                $query
                    ->WhereHas('subject', function ($query) use ($departmentId) {
                        $query->whereHas('department', function ($query) use ($departmentId) {
                            $query->where('departments.id', $departmentId);
                        })
                        ->withTrashed();
                    });
            })
            ->when($request->log_name ?? false, function ($query, $logName) {
                $query->where('log_name', 'like', "%{$logName}%");
            })

            ->when($request->ip ?? false, function ($query, $ip) {
                $query->where('activity_log.properties->ip', 'like', "%{$ip}%");
            })

            ->when($request->activity ?? false, function ($query, $activity) {
                $query->where('activity_log.properties->activity', 'like', "%{$activity}%");
            })

            ->when($request->target ?? false, function ($query, $target) {
                $query->where('activity_log.properties->target', 'like', "%{$target}%");
            })
            ->when($request->start_date && $request->end_date ?? false, function ($query) use ($request) {

                $query->whereBetween('created_at', $this->parseDate(
                    $request->start_date,
                    $request->end_date
                ));
            })
            ->when($dateRange, function ($query) use ($dateRange) {

                $query->whereBetween('created_at', $this->parseDate(...$dateRange));
            })

            ->when($request->username ?? false, function ($query, $username) {
                $query->whereHas('causer', function ($query) use ($username) {
                    $query->where('username', $username);
                });
            })

            ->when($request->sort_by == 'ip', function ($query) use ($request) {
                $query->orderBy('activity_log.properties->ip', $request->sort_type);
            })

            ->when($request->sort_by == 'activity', function ($query) use ($request) {
                $query->orderBy('activity_log.properties->activity', $request->sort_type);
            })

            ->when($request->sort_by == 'description', function ($query) use ($request) {
                $query->orderBy('activity_log.description', $request->sort_type);
            })

            ->when($request->sort_by == 'log_name', function ($query) use ($request) {
                $query->orderBy('activity_log.log_name', $request->sort_type);
            })

            ->when($request->sort_by == 'target', function ($query) use ($request) {
                $query->orderBy('activity_log.properties->target', $request->sort_type);
            })

            ->when($request->sort_by == 'date', function ($query) use ($request) {
                $query->orderBy('activity_log.created_at', $request->sort_type);
            })

            ->when($request->sort_by == 'username', function ($query) use ($request) {
                $query->whereHas('causer', function ($query) use ($request) {
                    $query->orderBy('username', $request->sort_type);
                });
            });
    }


    private function parseDate(string $startDate, string $endDate): array
    {
        $startDate = Carbon::parse($startDate)->startOfDay();
        $endDate   = Carbon::parse($endDate)->endOfDay();

        return [
            $startDate,
            $endDate,
        ];
    }
}
