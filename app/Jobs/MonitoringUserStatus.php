<?php

namespace App\Jobs;

use App\Events\UserStatusEvent;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class MonitoringUserStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $users = User::whereNotNull('last_performed_at')
            ->where('last_performed_at','<=',Carbon::now()->subMinutes(10))
            ->where('status',1)
            ->select('id', 'last_performed_at', 'status')
            ->get();

        foreach ($users as $user) {
            $performedAt = Carbon::parse($user->last_performed_at)->addMinutes(10);
            if ($performedAt < Carbon::now() && $user->status) {
                User::where('id', $user->id)->update(['status' => 0]);
                UserStatusEvent::dispatch($user);
            }
        }
    }
}
