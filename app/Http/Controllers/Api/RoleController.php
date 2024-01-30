<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Role\RoleRequest;
use App\Http\Resources\Api\RoleResource;
use App\Models\Role;
use App\Trait\Authorizable;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    use Authorizable;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $roles = Role::with(['department', 'permissions'])
            ->withCount('users')
            ->filter($request)->get();

        return RoleResource::collection($roles);
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(RoleRequest $request): JsonResponse
    {
        DB::beginTransaction();

        try {

            $role = Role::withTrashed()->where([
                'name' => $request->name,
                'department_id' => $request->department_id,
            ])->first();

            if ($role) {
                $role->restore();
            }

            if (!$role) {
                $role = Role::firstOrCreate([
                    'name' => $request->name,
                    'department_id' => $request->department_id,
                ]);
            }

            $role->permissions()->sync($request->permissions ?? []);

            activity("Role created")
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role created successfully",
                    'target' => "$role->name",
                ])
                ->log(":causer.name created Role $role->name.");

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Role Created Successfully!!',
                'data'    => new RoleResource($role),
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
     * Update the specified resource in storage.
     */
    public function update(RoleRequest $request, $id): JsonResponse
    {
        DB::beginTransaction();

        try {

            $role = Role::findOrFail($id);

            if ($role->name == AppConstant::ADMINISTRATOR) {
                throw new Exception("Cann't update Administrator", 422);
            }

            $role->update([
                'name' => $request->name,
            ]);

            $role->permissions()->detach();

            $role->permissions()->sync($request->permissions ?? []);

            activity("Role updated")
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role updated successfully",
                    'target' => "$role->name",
                ])
                ->log(":causer.name updated Role $role->name.");

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => ' Role Successfully Updated!!',
                'data' => new RoleResource($role),

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
    public function destroy(string $id): JsonResponse
    {
        DB::beginTransaction();
        try {


            $role = Role::findOrFail($id);

            $role->delete();

            $role->permissions()->detach();

            activity("Role deleted")
                ->causedBy(auth()->user())
                ->performedOn($role)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'activity' => "Role deleted successfully",
                    'target' => "$role->name",
                ])
                ->log(":causer.name deleted Role $role->name.");


            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Successfully Role Deleted!!',
                'data'    => $role,
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
