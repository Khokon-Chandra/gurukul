<?php

namespace App\Http\Controllers\Api;

use App\Constants\AppConstant;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\CashflowRequest;
use App\Http\Resources\Api\CashflowResource;
use App\Models\Cashflow;
use App\Trait\Authorizable;
use App\Trait\ParseActivity;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashflowController extends Controller
{
    use Authorizable, ParseActivity;
    /**
     * Display a listing of the resource.
     */
    public function index(CashflowRequest $request): AnonymousResourceCollection
    {
        $data = Cashflow::with('createdBy')
            ->filter($request)
            ->latest()
            ->paginate(AppConstant::PAGINATION);

        return CashflowResource::collection($data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CashflowRequest $request): JsonResponse
    {
        DB::beginTransaction();
        try {

            $cashflow = Cashflow::create($request->validated());

            activity('cashflow_created')->causedBy(Auth::id())
                ->performedOn($cashflow)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                    'target'   => $cashflow->name,
                    'activity' => 'Created cashflow',
                ])
                ->log('Created cashflow successfully');

            DB::commit();
            return response()->json([
                'status'  => 'success',
                'message' => 'Cashflow created successfully',
                'data'    => new CashflowResource($cashflow),
            ], 200);
        } catch (\Exception $error) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CashflowRequest $request, Cashflow $cashflow): JsonResponse
    {
        DB::beginTransaction();
        try {

            activity('cashflow_updated')->causedBy(Auth::id())
                ->performedOn($cashflow)
                ->withProperties([
                    'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                    'target'   => $cashflow->name,
                    'activity' => 'updated cashflow',
                ])
                ->log($this->parseUpdateAble($cashflow,$request->all()));

                $cashflow->update($request->validated());

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Cashflow updated successfully',
                'data'    => new CashflowResource($cashflow),
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Update multiple 
     */

    public function updateMultiple(CashflowRequest $request): JsonResponse
    {
        $attributes = $request->validated()['cashflows'];

        DB::beginTransaction();
        try {

            $idArr = [];

            foreach ($attributes as $attribute) {

                $idArr[] = $attribute['id'];

                $cashflow = Cashflow::find($attribute['id']);

                $cashflow->update($attribute);

                activity('cashflow_updated')->causedBy(Auth::id())
                    ->performedOn($cashflow)
                    ->withProperties([
                        'ip'       => Auth::user()->last_login_ip ?? $request->ip(),
                        'target'   => $cashflow->name,
                        'activity' => 'updated cashflow',
                    ])
                    ->log($this->parseUpdateAble($cashflow,$attribute));
            }

            DB::commit();

            return response()->json([
                'status'  => 'success',
                'message' => 'Multiple Cashflow updated successfully',
                'data'    => CashflowResource::collection(Cashflow::whereIn('id', $idArr)->get()),
            ], 200);
        } catch (\Exception $error) {

            DB::rollBack();

            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Cashflow $cashflow): JsonResponse
    {
        try {

            $cashflow->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Cashflows are deleted successfully'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }

    /**
     * Remove multiple resource from storage.
     */
    public function deleteMultiple(CashflowRequest $request): JsonResponse
    {
        try {

            Cashflow::whereIn('id', $request->cashflows)->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Cashflows are deleted successfully'
            ], 200);
        } catch (\Exception $error) {
            return response()->json([
                'status' => 'error',
                'message' => $error->getMessage()
            ], 500);
        }
    }
}
