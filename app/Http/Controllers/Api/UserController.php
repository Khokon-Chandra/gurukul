<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;

use App\Http\Resources\Api\UserResource;
use App\Http\Requests\Api\UserRequest;
use App\Models\User;
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
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $users = User::with('roles')->latest()->filter($request)->paginate(AppConstant::PAGINATION);
        return UserResource::collection($users);
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {

        $input = $request->validated();

        $input['created_by'] = Auth::id();
        $input['password'] = Hash::make($request->password);

        try {
            DB::beginTransaction();
            $user = User::create($input);
            $user->roles()->sync([$request->role]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw ValidationException::withMessages([$e->getMessage()]);
        }

        DB::commit();
        # log activity
        activity('create_user')->causedBy(Auth::user()->id ?? 1)
            ->performedOn($user)
            ->withProperties([
                'ip' => Auth::user()->last_login_ip ?? $request->ip(),
                'target' => $request->username,
                'activity' => 'Created user successfully',
            ])
            ->log('Created user successfully');
        return response()->json([
            'status' => 'successful',
            'message' => 'User Created Sucessfully',
            'data' => $user->load('roles'),
        ]);
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
    public function destroy($ids)
    {
        try {
            $ids = explode(',', $ids);

            foreach ($ids as $id_check) {
                $user = User::find($id_check);
                if (!$user) {
                    throw ValidationException::withMessages(["With Id $id_check Not Found, Please Send Valid data"]);
                }
            }

            foreach ($ids as $id) {
                $user = User::find($id);
                $user->update([
                    'deleted_by' => Auth::user()->id,
                    'deleted_at' => now(),
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
                'message' => 'User Successfully Deleted',
                'data' => null,
            ]);
        } catch (\Exception $e) {
            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function changePassword(Request $request): JsonResponse
    {
        $this->validate($request, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],

        ]);

        $userId = $request->id;

        $user = User::findorFail($userId);


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
       ]);

    }

}
