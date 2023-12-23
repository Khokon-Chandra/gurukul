<?php

namespace App\Http\Middleware;

use App\Events\UserStatusEvent;
use App\Models\User;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class UserAction
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $guards = empty($guards) ? [null] : $guards;

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {

                $prevStatus = Auth::user()->status;

                User::where('id',Auth::id())->update([
                    'last_performed_at' => Carbon::now(),
                    'status'            => 1,
                ]);

                if(!$prevStatus){
                    UserStatusEvent::dispatch(Auth::user());
                }
            }
        }

        return $next($request);
    }
}
