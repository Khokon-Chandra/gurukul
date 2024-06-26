<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;

use App\Http\Resources\Api\UserResource;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\GroupMemberResource;
use App\Models\User;
use App\Trait\CanSort;
use App\Trait\HasPermissionsStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController extends Controller
{
    use CanSort, HasPermissionsStructure;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::with('roles', 'department')->filter($request);
        $this->sortUserData($request, $query);
        $users = $query->latest()->paginate(AppConstant::PAGINATION);

        return UserResource::collection($users);
    }



    public function allUser(): AnonymousResourceCollection
    {
        $users = User::with('department')
            ->when(request('department_id') ?? false, function ($query, $id) {
                $query->where('department_id', $id);
            })
            ->select('id', 'department_id', 'name', 'username', 'last_login_at', 'status')->get();
        return GroupMemberResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */

    public function storeUser(UserRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {
            $input = $request->validated();
            $input['created_by'] = Auth::id();
            $input['password'] = Hash::make($request->password);
            $user = User::create($input);
            $user->roles()->sync([$request->role]);

            Cache::forget('dashboard');

            activity('create_user')->causedBy(Auth::user()->id ?? 1)
                ->performedOn($user)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip ?? $request->ip(),
                    'target' => $request->username,
                    'activity' => 'Created user successfully',
                ])
                ->log('Created user successfully');

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Created Successfully',
                'data' => $user->load('roles'),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }



    /**
     * Update user.
     */
    public function updateUser(UserRequest $request, User $user): JsonResponse
    {

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'role' => $request->role,
            'updated_by' => Auth::user()->id,
            'type'       => $request->type,
        ]);
        $user->roles()->sync([$request->role]);

        $user->load('roles');

        activity('update_user')->causedBy(Auth::user()->id)
            ->performedOn($user)
            ->withProperties([
                'ip' => Auth::user()->last_login_ip ?? $request->ip(),
                'target' => $request->name,
                'activity' => 'Update user successfully',
            ])
            ->log('Update user successfully');

        return response()->json([
            'status' => 'success',
            'message' => 'User Updated Successfully',
            'data' => new UserResource($user),
        ]);
    }

    /**
     * Remove the specified resource from storage.
     * @throws ValidationException
     */

    public function deleteUser(UserRequest $request): JsonResponse
    {
        try {
            $ids = $request->ids;

            foreach ($ids as $id) {
                $user = User::findOrFail($id);

                $user->update([
                    'deleted_by' => Auth::user()->id,
                ]);

                activity('user')->causedBy(Auth::user()->id)
                    ->performedOn($user)
                    ->withProperties([
                        'ip' => Auth::user()->last_login_ip,
                        'target' => $user->username,
                        'activity' => 'Deleted user',
                    ])
                    ->log(":causer.username deleted a user $user->username");

                $user->delete();
            }

            Cache::forget('dashboard');

            return response()->json([
                'status' => 'successful',
                'message' => 'Delete Operation Successful',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @throws ValidationException
     */
    public function changePassword(UserRequest $request, User $user): JsonResponse
    {
        $user->update([
            'password' => Hash::make($request->password)
        ]);

        activity("Password Updated")
            ->causedBy($user)
            ->performedOn($user)
            ->withProperties([
                'ip' => $user->last_login_ip,
                'activity' => "Password updated successfully",
            ])
            ->log(":causer.name updated Password");

        return response()->json([
            'status' => "successful",
            'message' => "Password Update Successful"
        ]);
    }
}
