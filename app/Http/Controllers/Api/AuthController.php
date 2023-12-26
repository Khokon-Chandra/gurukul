<?php

namespace App\Http\Controllers\Api;

use App\Events\UserStatusEvent;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\UserResource;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Trait\HasPermissionsStructure;

class AuthController extends Controller
{
    use HasPermissionsStructure;
    public function login(Request $request): JsonResponse
    {
        $this->validate($request, [
            'username' => ['required', 'min:2', 'exists:users'],
            'password' => 'required|min:5',
        ]);

        $user = User::where('username', $request->username)
            ->where('deleted_at', null)
            ->first();

        if (!$user->active) {
            return response()->json([
                'status' => 'error',
                'message' => 'Username has been deactivate!.',
                'permission_access' => false,
            ], 400);
        }

        $input = $request->only(['password', 'username']);


        if (!$token = auth()->attempt($input)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid Login Credentials',
                'permission_access' => false,
            ], 400);
        }

        $user->update([
            'timezone' => $request->timezone ?? null,
            'last_login_at' => Carbon::now(),
            'last_login_ip' => $request->ip() ?? $request->getClientIp() ?? "0.0.0.0",
            'remember_token' => $token,
        ]);



        activity('User Login')->causedBy(Auth::user()->id)
            ->performedOn($user)
            ->withProperties([
                'ip' => $request->ip(),
                'target' => $request->username,
                'activity' => 'User Login successfully',
            ])
            ->log('User Login successfully');

        return response()->json([
            'message' => 'Login Successful',
            'status' => 'success',
            'data' => [
                'token' => $token,
                'user' => new UserResource($user),
                'permission_access' => true,
                'permissions' => $this->pullAuthUserPermissionWithDataStructure(),
                'token_type' => 'Bearer',
            ],
        ], 200);
    }


    /**
     * Logout
     */

    public function logout(Request $request)
    {
        try {

            activity('User Logout')->causedBy(Auth::user()->id)
            ->performedOn(Auth::user())
            ->withProperties([
                'ip' => Auth::user()->last_login_ip,
                'target' => Auth::user()->username,
                'activity' => 'User Logout successfully',
            ])
            ->log(Auth::user()->username." Logout successfully");

            $this->triggerUserOfflineMode();

            $token = auth()->user();

            JWTAuth::parseToken()->invalidate($token);

            User::where('id', Auth::id())->update([
                'remember_token' => null
            ]);

            Auth::logout();

            return response()->json([
                'status' => 'success',
                'message' => 'Log-Out Successfully',
            ], 200);
        } catch (\Throwable $th) {
            Log::error($th);
            return response()->json([
                'status' => 'error',
                'message' => $th->getMessage(),
            ], 500);
        }
    }


    /**
     * When logout user : set active status as offline
     */
    private function triggerUserOfflineMode()
    {
        User::where('id',Auth::id())->update([
            'status' => false
        ]);

        UserStatusEvent::dispatch(Auth::user());
    }


}
