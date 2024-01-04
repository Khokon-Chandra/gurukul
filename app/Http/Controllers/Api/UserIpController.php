<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\UserIP\UserIpRequest;
use App\Http\Resources\Api\UserIpResource;
use App\Models\UserIp;
use App\Trait\Authorizable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserIpController extends Controller
{
    use Authorizable;
    public function index(UserIpRequest $request): AnonymousResourceCollection
    {

        $UserIps = UserIp::filter($request)
            ->latest()
            ->paginate(AppConstant::PAGINATION);

        return UserIpResource::collection($UserIps);
    }


    /**
     * @throws ValidationException
     */
    public function store(UserIpRequest $request): JsonResponse
    {
        if ($request->number3 === null && $request->number4 !== null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format IP Invalid!.',
            ], 422);
        }


        DB::beginTransaction();
        try {
            $ip1 = $request->number1;
            $ip2 = $request->number2;
            $ip3 = $request->number3;
            $ip4 = $request->number4;
            $ip = $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;
            $checkIps   = UserIp::where('ip', $ip)->count();

            if ($checkIps) {

                return response()->json([
                    'status'       => 'error',
                    'message'      => 'User Ip already exist',
                    'ip_whitelist' => $ip,
                ], 400);
            }

            // Insert to Database
            $payload = [
                'department_id' => $request->department_id,
                'ip'  => $ip,
                'description' => $request->description,
                'created_by'  => Auth::id(),
                'created_at'  => now(),
            ];

            $UserIp = UserIp::create($payload);

            // Create Activity Log
            activity('create user ip')->causedBy(Auth::user()->id)
                ->performedOn($UserIp)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $UserIp->ip,
                    'activity' => 'Create user ip',
                ])
                ->log('Successfully');

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Created Successfully',
                'data' => new UserIpResource($UserIp),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function update(UserIpRequest $request, $id)
    {
        $userIp = UserIp::findOrFail($id);
        if ($request->number3 === null && $request->number4 !== null) {
            return response()->json([
                'status' => 'error',
                'message' => 'Format IP Invalid!.',
            ], 422);
        }

        DB::beginTransaction();
        try {

            $ip1 = $request->number1;
            $ip2 = $request->number2;
            $ip3 = $request->number3;
            $ip4 = $request->number4;
            $ip = $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;

            $checkIps = UserIp::select('ip')
                ->where('ip', $ip)
                ->whereNotIn('id', [$userIp->id])
                ->count();

            if ($checkIps) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User Ip already exist',
                    'ip_whitelist' => $ip,
                ], 422);
            }


            // Update on Database
            $description = $request->description ?? $userIp->description;
            $department_id = $request->department_id ?? $userIp->department_id;
            $payload = [
                'department_id' => $department_id,
                'ip' => $ip,
                'whitelisted' => $request->whitelisted,
                'description' => $description,
                'updated_by' => Auth::id(),
                'updated_at' => now(),
            ];

            $dataUpdate = [];
            if ($userIp->department_id != $department_id) {
                $dataUpdate['department_id'] = 'Department ID : ' . $userIp->department_id . ' -> ' . $department_id;
            }
            if ($userIp->ip != $ip) {
                $dataUpdate['ip'] = 'IP Address : ' . $userIp->ip . ' -> ' . $ip;
            }
            if ($userIp->whitelisted != $request->whitelisted) {
                $dataUpdate['whitelisted'] = 'Whitelisted : ' . ($userIp->whitelisted == 1 ? 'True' : 'False') . ' -> ' . ($request->whitelisted == 1 ? 'True' : 'False');
            }
            if ($userIp->description != $description) {
                $dataUpdate['description'] = 'Description : ' . $userIp->description . ' -> ' . $description;
            }

            $dataLog = implode(', ', $dataUpdate) == null ? 'No Data Updated' : implode(', ', $dataUpdate);

            $userIp->update($payload);

            // Create Activity Log
            activity('update User ip')->causedBy(Auth::user()->id)
                ->performedOn($userIp)
                ->withProperties([
                    'ip' => Auth::user()->last_login_ip,
                    'target' => $userIp->ip,
                    'activity' => 'Updated User ip',
                ])
                ->log('Successfully Updated User ip, ' . $dataLog);

            DB::commit();

            return response()->json([
                'status'  => 'successful',
                'message' => 'User Ip Updated Successfully',
                'data'    => new UserIpResource($userIp),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * @throws ValidationException
     */
    public function multiUpdate(UserIpRequest $request): JsonResponse
    {

        try {
            DB::beginTransaction();
            $userIdData = [];
            $items = $request->input('items');
            foreach ($items as $item) {
                $id = $item['id'];

                if ($item['item']['number3'] === null && $item['item']['number4'] !== null) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'Format IP Invalid!.',
                    ], 400);
                }

                $UserIp = UserIp::find($id);

                if (!$UserIp) {
                    throw ValidationException::withMessages(["$UserIp Ip Not Found"]);
                }

                $ip1 = $item['item']['number1'];
                $ip2 = $item['item']['number2'];
                $ip3 = $item['item']['number3'];
                $ip4 = $item['item']['number4'];
                $ip = $ip1 . '.' . $ip2 . '.' . $ip3 . '.' . $ip4;
                $checkIps = UserIp::select('ip')->where('ip', 'LIKE', '%' . $ip1 . '.' . $ip2 . '%')->whereNotIn('id', [$id])->pluck('ip')->toArray();
                if ($checkIps != []) {

                    if (in_array($ip, $checkIps)) {
                        return response()->json([
                            'status' => 'error',
                            'message' => 'User Ip already exist',
                            'ip_whitelist' => $ip,
                        ], 400);
                    }
                }

                // Update on Database
                $description = $item['item']['description'] ?? $UserIp->description;
                $department_id = $item['item']['department_id'] ?? $UserIp->department_id;
                $payload = [
                    'department_id' => $department_id,
                    'ip' => $ip,
                    'whitelisted' => $item['item']['whitelisted'],
                    'description' => $description,
                    'updated_by' => auth()->user()->id,
                    'updated_at' => now(),
                ];

                $dataUpdate = [];
                if ($UserIp->department_id != $department_id) {
                    $dataUpdate['department_id'] = 'Department ID : ' . $UserIp->department_id . ' -> ' . $department_id;
                }
                if ($UserIp->ip != $ip) {
                    $dataUpdate['ip'] = 'IP Address : ' . $UserIp->ip . ' -> ' . $ip;
                }
                if ($UserIp->whitelisted != $request->whitelisted) {
                    $dataUpdate['whitelisted'] = 'Whitelisted : ' . ($UserIp->whitelisted == 1 ? 'True' : 'False') . ' -> ' . ($request->whitelisted == 1 ? 'True' : 'False');
                }
                if ($UserIp->description != $description) {
                    $dataUpdate['description'] = 'Description : ' . $UserIp->description . ' -> ' . $description;
                }

                $dataLog = implode(', ', $dataUpdate) == null ? 'No Data Updated' : implode(', ', $dataUpdate);

                $UserIp->update($payload);

                // Create Activity Log
                activity('update User ip')->causedBy(Auth::user()->id)
                    ->performedOn($UserIp)
                    ->withProperties([
                        'ip' => Auth::user()->last_login_ip,
                        'target' => $UserIp->ip,
                        'activity' => 'Updated User ip',
                    ])
                    ->log('Successfully Updated User ip, ' . $dataLog);

                $userIdData[] = $UserIp;
            }

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'Users Ip Updated Successfully',
                'data' => UserIpResource::collection($userIdData),
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e);

            throw ValidationException::withMessages([$e->getMessage()]);
        }
    }

    /**
     * @throws ValidationException
     */
    public function destroy($id): JsonResponse
    {
        $userIp = UserIp::findOrFail($id);
        
        DB::beginTransaction();
        try {

            activity('user_ip')->causedBy(Auth::id())
                ->performedOn($userIp)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip,
                    'target'   => $userIp->ip,
                    'activity' => 'Deleted user ip',
                ])
                ->log('Deleted Successfully');
            $userIp->delete();


            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Successfully Deleted',
                'data' => null,
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }



    public function deleteMultiple(UserIpRequest $request): JsonResponse
    {

        DB::beginTransaction();
        try {

            foreach ($request->items as $id) {
                $userIp = UserIp::find($id);
                activity('user_ip')->causedBy(Auth::id())
                    ->performedOn($userIp)
                    ->withProperties([
                        'ip'       => Auth::user()->last_login_ip,
                        'target'   => $userIp->ip,
                        'activity' => 'Deleted user ip',
                    ])
                    ->log('Deleted Successfully');
                $userIp->delete();
            }

            DB::commit();

            return response()->json([
                'status' => 'successful',
                'message' => 'User Ip Successfully Deleted',
                'data' => null,
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
