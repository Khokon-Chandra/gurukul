<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserRequest;
use App\Http\Resources\Api\PermissionChildResource;
use App\Http\Resources\Api\PermissionResource;
use App\Http\Resources\Api\GroupMemberResource;
use App\Http\Resources\Api\User\UserResource;
use App\Models\User;
use App\Trait\CanSort;
use App\Trait\HasPermissionsStructure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    use CanSort, HasPermissionsStructure;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = User::with('roles')->filter($request);
        $this->sortUserData($request, $query);
        $users = $query->latest()->paginate(AppConstant::PAGINATION);

        return UserResource::collection($users);

    }



    public function allUser(): AnonymousResourceCollection
    {
        $users = User::select('id','name','username','last_login_at','status')->get();
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
    public function update(UserRequest $request, User $user)
    {

        $input = $request->validated();
        $input['updated_by'] = Auth::user()->id;
        $user->update($input);
        $user->roles()->sync([$request->role]);

        $user->load('roles');

        activity('update_user')->causedBy(Auth::user()->id)
            ->performedOn($user)
            ->withProperties([
                'ip' => Auth::user()->last_login_ip,
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
                        'user' => Auth::user()->last_login_ip,
                        'target' => $user->ip_address,
                        'activity' => 'Deleted user',
                    ])
                    ->log('Successfully');

                $user->delete();
            }

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
    public function changePassword(Request $request): JsonResponse
    {

        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed']
        ]);

        $user = Auth::user();

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
